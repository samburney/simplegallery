<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class SifntUploadDBMigrate extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'command:sifntdbmigrate';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		$olddbname = $this->argument('olddbname');
		$oldpath = substr($this->argument('oldpath'), 0, 1) == '/' ? $this->argument('oldpath') : getcwd() . '/' . $this->argument('oldpath');

		// Disable QueryLog
		DB::connection()->disableQueryLog();
		DB::connection($olddbname)->disableQueryLog();

		// Setup sifntFileUpload instance
		$sifntFileUpload = new sifntFileUpload();

		// List of relevant user ids
		$users = DB::connection($olddbname)
			->table('sifntupload_files')
			->groupBy('sifntupload_files.user_id')
			->join('phpbb_users', 'sifntupload_files.user_id', '=', 'phpbb_users.user_id')
			->select('phpbb_users.user_id as id', 'phpbb_users.username as username', 'phpbb_users.user_email as email')
			->get();

		foreach($users as $old_user) {
			if(!$user = User::where('username', '=', $old_user->username)->orWhere('email', '=', $old_user->email)->first()) {
				// Create new user
				$user = new User;
				$user->username = $old_user->username;
				$user->email = $old_user->email;
				$user->save();
			}
			echo "Processing files for " . $user->username . "\n";

			// process this user's files
			$files = DB::connection($olddbname)
				->table('sifntupload_files')
				->where('user_id', '=', $old_user->id)
				->leftJoin('sifntupload_images', 'sifntupload_images.file_id', '=', 'sifntupload_files.file_id')
				->orderBy('sifntupload_files.file_id', 'asc')
				->get();

			foreach($files as $file) {
				// Build $uploaddata array
				$uploaddata = array(
					'user_id' => $user->id,
					'tmp_name' => "$oldpath/$file->file_name.$file->file_ext",
					'copy_mode' => 'copy',
					'file_type' => $file->file_type,
					'file_size' => $file->file_size,
					'file_name' => $file->file_cleanname,
					'file_ext' => $file->file_ext,
					'file_fullname' => "$file->file_originalname.$file->file_ext",
					'file_description' => $file->file_description,
					'file_datename' => $file->file_name,
					'file_cleanname' => $file->file_cleanname,
					'file_extra' => $file->file_extra,
				);

				if($file->file_extra == 'image'){
					if($uploaddata['image_type'] = $file->image_type) {
						$uploaddata['image_width'] = $file->image_width;
						$uploaddata['image_height'] = $file->image_height;
						$uploaddata['image_bits'] = $file->image_bits;
						$uploaddata['image_channels'] = $file->image_channels;
					}
					elseif($image_data = getimagesize($uploaddata['tmp_name'])) {
						$uploaddata['image_type'] = $image_data['mime'];
						$uploaddata['image_width'] = $image_data[0];
						$uploaddata['image_height'] = $image_data[1];
						$uploaddata['image_bits'] = @$image_data['bits'] ? $image_data['bits'] : 0;
						$uploaddata['image_channels'] = @$image_data['channels'] ? $image_data['channels'] : 0;
					}
					else {
						echo "Error: " . $uploaddata['tmp_name'] . " is an image but doesn't have image data; aborting.";
					}
				}

				$file_name = public_path() . "/files/" . $uploaddata['file_datename'] . '.' . $uploaddata['file_ext'];
				if(!file_exists($file_name)) {
					echo "Processing: $file_name   ";
					if($sifntFileUpload->processFile($uploaddata)) {
						echo "Done.\n";

						// Add collection and tag based on group name
						$group_name = DB::connection($olddbname)
							->table('sifntupload_filegroups')
							->where('filegroup_id', '=', $file->filegroup_id)
							->pluck('filegroup_name');

						if($group_name != 'Unfiled') {
							if(!$collection = Collection::where('name', '=', $group_name)->where('user_id', '=', $user->id)->first()) {
								$collection = new Collection;

								$collection->name = $group_name;
								$collection->name_unique = sifntFileUtil::cleantext($group_name, "collections");
								$collection->user_id = $user->id;

								$collection->save();
							}
							$collection->uploads()->attach($uploaddata['file_id']);
							TagController::processTags($uploaddata['file_id'], [str_singular(sifntFileUtil::cleantext($collection->name))], true);
						}

						// Process old tags
						$old_tags = DB::connection($olddbname)->table('sifntupload_filetags')
							->where('file_id', '=', $file->file_id)
							->lists('tag');
						TagController::processTags($uploaddata['file_id'], $old_tags, true);
					}
					else {
						echo "Error!\n";
						unlink($filename);
					}
				}
				else {
					echo "Error: $file_name already exists\n";
				}
			}
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('olddbname', InputArgument::REQUIRED),
			array('oldpath', InputArgument::REQUIRED),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
		);
	}

}