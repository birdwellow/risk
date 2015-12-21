@extends('app')

@section('content')

<div toggle-for="tour-carousel">
    Toggle
</div>

<div class="container-fluid">
	<div class="row">
            <div class="col-md-8 col-md-offset-2">
                
                <div id="tour-carousel" class="carousel slide" data-ride="carousel">
                    <ol class="carousel-indicators">
                        <li data-target="#tour-carousel" data-slide-to="0" class="active"></li>
                        <li data-target="#tour-carousel" data-slide-to="1"></li>
                        <li data-target="#tour-carousel" data-slide-to="2"></li>
                        <li data-target="#tour-carousel" data-slide-to="3"></li>
                    </ol>
                    
                    <div class="carousel-inner" role="listbox">
                        <div class="item active">
                            <div class="carousel-caption">
                                Capt. 1
                            </div>
                            <img src="/img/tour/1.jpg">
                        </div>
                        
                        <div class="item">
                            <div class="carousel-caption">
                                Capt. 2
                            </div>
                            <img src="/img/tour/2a.jpg">
                        </div>
                        
                        <div class="item">
                            <div class="carousel-caption">
                                Capt. 3
                            </div>
                            <img src="/img/tour/3.jpg">
                        </div>
                    </div>
                    
                    <a class="left carousel-control" href="#tour-carousel" role="button" data-slide="prev">
                        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                        <span class="sr-only">
                            Previous
                        </span>
                    </a>
                    <a class="right carousel-control" href="#tour-carousel" role="button" data-slide="next">
                        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                        <span class="sr-only">
                            Next
                        </span>
                    </a>
                </div>
                
            </div>
        </div>
</div>

@endsection