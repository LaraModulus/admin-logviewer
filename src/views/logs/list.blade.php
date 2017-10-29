@extends('admincore::layouts.dashboard')
@section('content')
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Logs</h1>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
        <div class="row">
            <div class="col-xs-12">
                <form class="form-inline" method="get" action="">
                    <div class="form-group">
                        <select name="file" id="file" class="form-control" title="Log file">
                            <option value="">All</option>
                            @foreach($files as $file)
                                <option value="{{basename($file)}}" @if(request('file')==basename($file)) selected @endif> {{basename($file)}} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <select name="level" id="level" class="form-control" title="Error level">
                            <option value="">All</option>
                            <option value="error" @if(request('level')=='error') selected @endif>Error</option>
                            <option value="debug" @if(request('level')=='debug') selected @endif>Debug</option>
                            <option value="warning" @if(request('level')=='warning') selected @endif>Warning</option>
                            <option value="warning" @if(request('level')=='info') selected @endif>Info</option>
                        </select>
                    </div>
                    @if(request('file'))
                        <div class="form-group pull-right">
                            <a class="btn btn-danger require-confirm" href="{{route('admin.logs.delete.file', ['file' => request('file')])}}">Delete selected file</a>
                        </div>
                    @endif
                    <button type="submit" class="btn btn-default">Filter</button>
                </form>

            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="table-responsive">
                    <table class="table table-striped" id="items_table">
                        <thead>
                        <tr>
                            <th>Level</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th><i class="fa fa-cogs"></i></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($logs as $log)
                        <tr>
                            <td>{{$log->level}}</td>
                            <td>{{$log->context->message}}</td>
                            <td>{{$log->date->format('d.m.Y H:i')}}</td>
                            <td style="min-width: 100px">
                                <div class="btn-group">
                                    <a class="btn btn-primary" href="javascript:;" data-toggle="modal" data-target="#logsModal" data-id="{{$log->id}}"><i class="fa fa-eye"></i></a>
                                    <a class="btn btn-danger require-confirm" href="{{route('admin.logs.delete', ['id' => $log->id])}}"><i class="fa fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4">{{$logs->links()}}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

    </div>
@stop
@section('modals')

    <div class="modal fade" id="logsModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Log #</h4>
                </div>
                <div class="modal-body">
                    <p class="text-center"><i class="fa fa-spinner fa-pulse fa-3x"></i></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@stop
@section('js')
    <script>
        $('#logsModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var modal = $(this);
            modal.find('.modal-title').text('Log #' + id);
            $.ajax({
                url: '{{route('admin.logs.form')}}?id='+id,
                beforeSend: function(){
                    modal.find('.modal-body').html('<p class="text-center"><i class="fa fa-spinner fa-pulse fa-3x"></i></p>');
                },
                success: function(data, textStatus, jqXHR){
                    modal.find('.modal-body').html(parseLog(data));
                },
                dataType: 'json',
                cache: false
            })
        });
        function parseLog(log){
            var div = document.createElement('div');
            var title = document.createElement('h3');
            title.innerText = log.context.message;
            div.appendChild(title);
            if(log.context.in){
                var subtitle = document.createElement('h6');
                subtitle.innerText = log.context.in +' at line #' + log.context.line;
                div.appendChild(subtitle);
            }
            var date = document.createElement('p');
            var d = new Date(log.date.date);
            date.innerHTML = '<i class="fa fa-calendar"></i> '+d.getDate()+'.'+d.getMonth()+'.'+d.getFullYear()+' '+d.getHours()+':'+d.getMinutes();

            var ul = document.createElement('ul');
            ul.className = 'list-group';
            for(var i=0; i<log.stack_traces.length;i++){
                var li = document.createElement('li');
                li.className = 'list-group-item';
                li.innerHTML = 'Caught at: '+log.stack_traces[i].caught_at+' <br />'+'In: '+log.stack_traces[i].in+' at #'+log.stack_traces[i].line;
                ul.appendChild(li);
            }

            div.appendChild(date);

            if(log.source && log.source.length){
                var source = document.createElement('pre');
                var code = document.createElement('code');
                var code_list = document.createElement('ol');
                code_list.setAttribute('type', '1');
                code_list.setAttribute('start', log.context.line-4);
                code.className = 'php';
                var line_num = log.context.line-4;
                for(var l=0; l<log.source.length; l++){
                    var line = document.createElement('li');
                    if(line_num==log.context.line){
                        line.style.color = 'red';
                    }
                    line.innerText = log.source[l];
                    code_list.appendChild(line);
                    line_num++;
                }
                code.appendChild(code_list);

                source.appendChild(code);

                div.appendChild(source);
            }

            div.appendChild(ul);

            return div;
        }
    </script>
@stop