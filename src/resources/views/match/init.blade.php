@extends('app')

@section('content')
    
<script>
    var username = "{{ Auth::user()->name }}";
</script>

<div class="container">
    
        <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                        <div class="panel-heading">Create a new Match</div>

                        <div class="panel-body">
                            {!! Form::open(array('url' => 'match/create')) !!}
                            <table>
                                <tr>
                                    <td>
                                        Name:
                                    </td>
                                    <td>
                                        {!! Form::text('name') !!}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Map:
                                    </td>
                                    <td>
                                        {!! Form::text('map_id') !!}
                                    </td>
                                </tr>
                            </table>
                            {!! 
                                Form::submit('Create!', array(
                                    'class' => 'btn btn-primary'
                                ))
                            !!}
                            {!! Form::close() !!}
                        </div>
                </div>
        </div>
</div>
@endsection
