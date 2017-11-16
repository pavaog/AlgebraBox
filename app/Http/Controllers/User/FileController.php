<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Storage;

class FileController extends Controller
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
    * Upload files.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
   public function store(Request $request)
   {

      $dir = $request->curr_dir;
      $massages = array();
      $massage_OK='';
      $massage_NO = '';

      if ($request->hasFile('file')) {
         foreach ($request->file as $file) {
            $fileName = $file->getClientOriginalName();
            if($file->getError()){
               $massage_NO .= ' ['.$fileName.']';
            } else {
               $file->storeAs('public/'.session('root').'/'.session('curr_dir').'/', $fileName);
               $massage_OK .= ' ['.$fileName.']';
            }
         }

         ($massage_OK) ? $massages['success'] = 'Files '.$massage_OK.' are uploaded.':'';
         ($massage_NO) ? $massages['warning'] = 'Files '.$massage_NO.' are not uploaded (max_file_size = 2MB) !':'';

         return redirect()->back()->with($massages);
      }
      return redirect()->back()->with(['warning' => 'No file selected or file size exceeded max size.']);
   }

   /**
    * Delete file.
    *
    * @param  string  $file
    * @return \Illuminate\Http\Response
    */
   public function delete($file)
   {
      $file_path = 'public/' . session('root') . '/' . session('curr_dir') . $file;
      if(Storage::exists($file_path)){
         Storage::delete($file_path);
         return redirect()->back()->with(['success' => 'File <strong>( ' . $file . ' )</strong> is deleted.']);
      }
      return redirect()->back()->with(['error' => 'Whoops, looks like something went wrong']);
   }

   /**
    * Download file from user directory.
    *
    * @param  string  $file
    * @return \Illuminate\Http\Response
    */
   public function download($file)
   {
      $file = 'public/' . session('root') . '/' . session('curr_dir') . '/' . $file;
      return response()->download(Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix().$file);
   }
}
