<?php

namespace LaraMod\Admin\Logs\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Jackiedo\LogReader\LogReader;

class LogsController extends Controller
{

    private $data = [];
    protected $reader;

    public function __construct(LogReader $reader)
    {
        config()->set('admincore.menu.logs.active', true);
        $this->reader = $reader;
    }

    public function index(Request $request)
    {
        $logs = $this->reader->level($request->get('level'))->filename($request->get('file'));
        $this->data['logs'] = $logs->orderBy('date','desc')->paginate(10, null, ['path' => route('admin.logs')]);
        $this->data['files'] = $this->reader->getLogFilenameList();
        return view('adminlogs::logs.list', $this->data);
    }

    public function getForm(Request $request)
    {
        $log = $this->reader->find($request->get('id'));
        $source_code = [];
        if($log->context->in && file_exists($log->context->in)){
            $lines = file($log->context->in);
            $start_line = $log->context->line-5;
            if($start_line<0) $start_line = 0;
            for($i=$start_line;$i<$log->context->line+5; $i++){
                $source_code[]=$lines[$i];
            }
            $log->source = $source_code;
        }
        $this->data['log'] = $log;
        if($request->wantsJson()){
            return response()->json($this->data['log']);
        }
        return view('adminlogs::logs.form', $this->data);
    }

    public function delete(Request $request)
    {
        if (!$request->has('id')) {
            return redirect()->route('admin.logs')->with('message', [
                'type' => 'danger',
                'text' => 'No ID provided!',
            ]);
        }
        try {
            $this->reader->find($request->get('id'))->delete();
        } catch (\Exception $e) {
            return redirect()->route('admin.logs')->with('message', [
                'type' => 'danger',
                'text' => $e->getMessage(),
            ]);
        }

        return redirect()->route('admin.logs')->with('message', [
            'type' => 'success',
            'text' => 'Log item deleted',
        ]);
    }

    public function deleteFile(Request $request)
    {
        try {
            $this->reader->filename($request->get('file'))->removeLogFile();
        } catch (\Exception $e) {
            return redirect()->route('admin.logs')->with('message', [
                'type' => 'danger',
                'text' => $e->getMessage(),
            ]);
        }

        return redirect()->route('admin.logs')->with('message', [
            'type' => 'success',
            'text' => 'Log item deleted',
        ]);
    }


}