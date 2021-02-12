@extends('layouts.app')

@section('content')
<div id="homeScreen" v-cloak>
    <div class="row">
        <div style="padding-left: 40%;padding-bottom: 10px;">

            <input type="search" placeholder="Please enter book name here"  v-model="searchInput" id="books" style="
            width: 250px;
            padding: 5px 10px;
        "> 
        
            <button class="btn btn-primary" @click="searchBooks()">Search Books</button>
        
        </div>
    </div>

    <div class="row" id="bookResult" style="padding: 0 55px">
        <div class="col-4" style="margin-bottom:20px" v-for="(book,index) in books">
            <div class="card">

                <img :src="(book.volumeInfo.imageLinks.thumbnail ?? '')" class="card-img-top" alt="Book-Image" style="
                height: 250px;
                width: 250px;
                align-self: center;
                margin: 15px;
            ">
                <div class="card-body" style="text-align: center;">
                    <h4 class="card-title">Title : @{{book.volumeInfo.title}}</h4>
                    <h4 class="card-title">Sub Title : @{{book.volumeInfo.subtitle}}</h4>
                    <h6 class="card-subtitle mb-2 text-muted book-description">Description : @{{book.volumeInfo.description}}</h6>
                    <p class="card-text">Authors : @{{ (book.volumeInfo.authors ? book.volumeInfo.authors.join(', ') : '')  }}</p>
                    <p class="card-text">Category : @{{ (book.volumeInfo.categories ? book.volumeInfo.categories.join(', ') : '')}}</p>
                    <p class="card-text">Average Ratting : @{{book.volumeInfo.averageRating}}</p>
                    
                    <a :href='book.volumeInfo.infoLink' target="_blank" class="btn btn-primary">View</a>

                    <a href="javascript:void(0)" @click="addToWishList(book)" class="btn btn-primary">Add To My List</a>
                </div>
            </div>

        </div>
    </div>   
   
</div>

@endsection
@section('header_styles')
<style>
 .book-description {
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2; /* number of lines to show */
        -webkit-box-orient: vertical;
    }

</style>
@endsection

@section('footer_scripts')
<script src="{{ mix('js/homeScreen.js') }}"></script>
@endsection
