// mixins
import { toastedMessages } from './components/mixins/toastedMessages'


import 'vue-toasted/dist/vue-toasted.min.css';

var HomeScreen = new Vue({
    el: '#homeScreen',
    
    http    :   {
        headers: {
            'X-CSRF-Token': $('meta[name=_token]').attr('content')
        }
    },
    mixins: [ toastedMessages ],
    
    data(){
        return {
            books : [],
            searchInput : '',
        }
    },
    methods : {
        searchBooks(){
            
            axios.get('https://www.googleapis.com/books/v1/volumes?q='+this.searchInput)
            .then((response) => 
                    this.books = response.data.items
            )
            .catch(error => {
                this.toastedErrorMessage(error.response.data.message)
            });
        },
        addToWishList(book){

            axios.post('/user/book/add-to-my-read-list', {
                book : JSON.stringify(book)
              })
            .then((response) => 
                this.toastedSuccessMessage(response.data.message)
            )
            .catch(error => {
                this.toastedErrorMessage(error.response.data.message)
            });
        }
    }
})