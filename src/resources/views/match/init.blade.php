@extends('app')

@section('content')
    
<script>
    var username = "{{ Auth::user()->name }}";
</script>

<div class="container">
    
        <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default game-panel">
                        <div class="panel-heading">{{ Lang::get('message.title.init.match') }}</div>

                        <div class="panel-body">
                            {!! Form::open(
                                array(
                                    'url' => route('match.create'),
                                    'class' => 'form-horizontal',
                                    'role' => 'form',
                                )
                            ) !!}
                            <div class="form-group">
                                <label class="col-md-4 control-label">
                                    {{ Lang::get('message.field.match.name') }}
                                </label>
                                <div class="col-md-6">
                                    {!! Form::text('name') !!}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label">
                                    {{ Lang::get('message.field.match.map') }}
                                </label>
                                <div class="col-md-6">
                                    {!! Form::text('map_id') !!}
                                </div>
                            </div>
                            {!! 
                                Form::submit(Lang::get('message.button.match.create'), array(
                                    'class' => 'btn btn-primary'
                                ))
                            !!}
                            {!! Form::close() !!}
                        </div>
                </div>
        </div>
</div>
@endsection
