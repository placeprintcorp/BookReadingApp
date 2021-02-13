@extends('layouts.app')

@section('content')
<div id="ViewBookScreen">
   
    <div class="row" id="bookResult" style="padding: 0 55px">
        <div class="col-12" style="margin-bottom:20px">
            <div class="card">

                <img src="{{$book->book_img_url}}" class="card-img-top" alt="Book-Image" style="
                height: 250px;
                width: 250px;
                align-self: center;
                margin: 15px;
            ">
                <div class="card-body" style="text-align: center;">
                    <h4 class="card-title">Title : {{ $book->book_title}}</h4>
                    <h6 class="card-subtitle mb-2 text-muted book-description">Description : {{ $book->book_description}}</h6>
                    <p class="card-text">Authors : {{ $book->authors}}</p>
                    <p class="card-text">Category : {{ $book->categories}}</p>
                    <p class="card-text">Average Ratting : {{ $book->average_rating}} </p>

                    <a href="{{ route('book.readList') }}" class="btn btn-primary">Back</a>


                    <a href="{{ $book->book_info_link }}" target="_blank" class="btn btn-primary">View More</a>
                    
                </div>
            </div>

        </div>
    </div>   
   
</div>

@endsection
