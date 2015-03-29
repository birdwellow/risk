@extends('app')

@section('content')
    
<script>
    var username = "{{ Auth::user()->name }}";
</script>

<div class="container">
    
        <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                        <div class="panel-heading">All matches</div>

                        <div class="panel-body">
                            <table style="width:100%;">
                                <tr>
                                    <td>
                                        Name
                                    </td>
                                    <td>
                                        Users
                                    </td>
                                    <td>
                                        Date
                                    </td>
                                    <td>
                                        Created By
                                    </td>
                                    <td>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                
                            @foreach($matches as $match)
                                <tr>
                                    <td>
                                        {{ $match->name }}
                                    </td>
                                    <td>
                                        @foreach($match->joinedUsers as $joinedUser)
                                            {{ $joinedUser->name }}
                                        @endforeach
                                    </td>
                                    <td>
                                        {{ $match->created_at }}
                                    </td>
                                    <td>
                                        {{ $match->createdBy->name }}
                                    </td>
                                    <td>
                                        <a href="/match/{{ $match->id }}">Join</a>
                                    </td>
                                    <td>
                                        @if(Auth::user()->id == $match->createdBy->id)
                                            <a href="/match/cancel/{{ $match->id }}">Delete</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </table>
                        </div>
                </div>
        </div>
</div>
@endsection
