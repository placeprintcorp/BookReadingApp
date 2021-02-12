<?php

namespace App\Http\Controllers;

use App\Models\BookToRead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookToReadController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('myReadList');
    }

    /**
     * Return a collection of all books.
     *
     * @return \Illuminate\Http\Response
     */
    public function getMyReadListDAta(Request $request){
        extract(request()->only(['query', 'limit', 'page', 'orderBy', 'ascending', 'byColumn']));
        
        $dbQuery = BookToRead::where('user_id',Auth::id())
        ->when($query, function($q) use ($query){
            $q->where('book_title','like', '%' . $query . '%');
        })
        ->limit($limit)->skip($limit * ($page - 1));

        if (isset($orderBy)) {
            $direction = $ascending == 1 ? 'ASC' : 'DESC';
            $dbQuery->orderBy($orderBy, $direction);
        } else {
            $dbQuery->latest();
        }

        $books = $dbQuery->get()->toArray();


        $count = BookToRead::where('user_id',Auth::id())->count();
        return [
            'data'  => $books,
            'count' => $count,
        ];
    }

    
    /**
     * Store a Book to user's read list.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $book = json_decode($request->book,true);
        
        $bookDataArray = [
            'user_id' => Auth::id(),
            'book_id' => $book['id'],
            'book_title' => $book['volumeInfo']['title'],
            'book_description' => $book['volumeInfo']['description'] ?? NULL,
            'authors' => (isset($book['volumeInfo']['authors']) ? implode(",",$book['volumeInfo']['authors']) : NULL),
            'categories' =>  (isset($book['volumeInfo']['categories']) ? implode(",",$book['volumeInfo']['categories']) : NULL),
            'average_rating' => $book['volumeInfo']['averageRating'] ?? NULL,
            'book_img_url' => $book['volumeInfo']['imageLinks']['thumbnail'],
            'book_info_link' => $book['volumeInfo']['infoLink'],
        ];

        BookToRead::where(['user_id'=>Auth::id(),'book_id'=>$book['id']])->updateOrCreate($bookDataArray);

        return response()->json([
            'status' => 200,
            'message' => 'Book has been added to your read list successfully',
          ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BookToRead  $bookToRead
     * @return \Illuminate\Http\Response
     */
    public function show($bookToRead)
    {
        $book = BookToRead::where(['user_id'=>Auth::id(),'book_id'=>$bookToRead])->first();
        if(!$book){
            redirect()->back();
        }
        return view('book',compact('book'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BookToRead  $bookToRead
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$bookId)
    {
       
        $bookToRead = BookToRead::where(['user_id'=>Auth::id(),'book_id'=>$bookId])->first();
        if(!$bookToRead){
            return response()->json([
                'status' => 404,
                'message' => "User's Book not found",
            ],404);
        }
        $bookToRead->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Book has been deleted from your read list successfully',
          ], 200);
    }
}
