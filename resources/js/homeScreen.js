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
            //var key = $('#googleBookKey').val;
            axios.get('https://www.googleapis.com/books/v1/volumes?q='+this.searchInput+'&key=AIzaSyCVaGoqKkMzMGAvTkWJ00W47GleDzsN3ns')
            .then((response) => 
                    this.books = response.data.items
            )
            .catch(error => {
                this.toastedErrorMessage(error.response.data.message)
            });
        },
        addToWishList(book){
            
            var baseUrl = $('#baseUrl').val();
            axios.post(baseUrl+'/user/book/add-to-my-read-list', {
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