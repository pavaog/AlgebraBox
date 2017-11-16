<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Requests\CreateDirRequest;
use App\Http\Controllers\Controller;
use Sentinel;
use App\Models\UserRoot;
use Storage;

class HomeController extends Controller
{
    /**
    * Set middleware to quard controller.
    *
    * @return void
    */
    public function __construct()
    {
        $this->middleware('sentinel.auth');
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index()
    {
        if (session()->has('root')) {
            $root_dir = session('root');
            session(['curr_dir' => '']);
        } else {
            $user_id = Sentinel::getUser()->id;
            $root = UserRoot::where('user_id', $user_id)->get(['name'])->first();
            $root_dir = $root->name;
            session(['root' => $root_dir]);
        }

        // List directories in current user directory
        $directories = $this->listDirs('');

        // List files in current user directory
        $files = $this->listFiles(session('root'));

        // Breadcrumbs array
        $breadcrumbs = array();
        // Current name directory
        $currDirName = '';

        // Up directory
        $upDirLink = '';
        // dd($directories, $files, $currDirName, $upDirLink, $breadcrumbs);

        return view('user.home')
                    ->with('directories', $directories)
                    ->with('files', $files)
                    ->with('breadcrumbs', $breadcrumbs)
                    ->with('curr_dir', $currDirName)
                    ->with('level_up', $upDirLink);
    }

    /**
    * Browse directory.
    *
    * @return \Illuminate\Http\Response
    */
    public function directories($dir = null)
    {
        session(['curr_dir' => $dir.'/']);
        $curr_dir = ltrim($dir,'/');

        // List directories in current user directory
        $directories = $this->listDirs($curr_dir);

        // List files in current user directory
        $files = $this->listFiles(session('root').'/'.$dir);

        // Breadcrumbs array
        $breadcrumbs = $this->breadcrumbs($dir);

        // Current name directory
        end($breadcrumbs);
        $lastDirKey = key($breadcrumbs);
        $currDirName = $breadcrumbs[$lastDirKey]['name'];

        // Up directory
        $upDirLink = rtrim($curr_dir,'/'.$currDirName);

        return view('user.home')
                    ->with('directories', $directories)
                    ->with('files', $files)
                    ->with('breadcrumbs', $breadcrumbs)
                    ->with('curr_dir', $currDirName)
                    ->with('currDirName', $currDirName)
                    ->with('level_up', $upDirLink);
    }

    /**
    * Create new directory.
    *
    * @return \Illuminate\Http\Response
    */
    public function create(CreateDirRequest $request)
    {
        if($request->new_dir){
            $new_dir = ('public/'.session('root').'/'.session('curr_dir').'/'.$request->new_dir);
            Storage::makeDirectory($new_dir);

            return redirect()->back()->with(['success' => 'Folder <strong>( ' . $request->new_dir . ' )</strong> is created.']);
        }
        return redirect()->back()->with(['warning' => 'Define Folder name!']);
    }

    /**
     * Delete directory.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete($dir)
    {
        $dirForDelete = 'public/' . session('root') .'/'. session('curr_dir') .'/'. $dir;

        if(Storage::deleteDirectory($dirForDelete))
        {
             return redirect()->back()->with(['success' => 'Folder <strong>( ' . $dir . ' )</strong> is deleted.']);
        }
        return redirect()->back()->with(['error' => 'Whoops, looks like something went wrong']);
    }

    /**
     * List of files in specific directory.
     *
     * @return void
     */
    private function listDirs($dir)
    {
        $allDirs = Storage::disk('public')->directories(session('root').'/'.$dir);
        $dirs = array();
        foreach ($allDirs as $value) {
            $fullDirName = explode("/",$value);
            $dirName = end($fullDirName);
            if($dir == ''){
                $dirs[$dirName] = $dirName;
            } else {
                $dirs[$dirName] = $dir.'/'.$dirName;
            }
        }
        return $dirs;
    }

    /**
     * List of files in specific directory.
     *
     * @return void
     */
    private function listFiles($dir)
    {
        $allFiles = Storage::files('public/' .$dir);
        // dd($allFiles);
        $files = array();
        $sumFiles = 0;
        foreach ($allFiles as $key => $value) {
            $file = explode("/",$value);
            $fileName = end($file);
            $ext = explode(".",$fileName);
            $fileType = end(($ext));
            $fileSize = Storage::size($value);
            $sumFiles = $sumFiles + $fileSize;
            $files[] = [$key, $fileName, $fileType, $fileSize];
        }
        return $files;
    }

    /**
     * Breadcrumbs.
     *
     * @return void
     */
    private function breadcrumbs($dir)
    {
        if ($dir) {
            $dirs = explode('/', ltrim($dir,'/'));
            $levels = array();
            $link = '';
            foreach ($dirs as $value) {
                $name = $value;
                $link = $link . '/' . $name;
                $levels[] = ['name' => $name,
                             'path' => ltrim($link,'/')];
            }
            return $levels;
        }
    }
}
