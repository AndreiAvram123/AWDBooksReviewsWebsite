
function configureInputListener(field){
    field.addEventListener('input',(event)=>{
      fetchSearchResults(event.target.value)
    })

}

function fetchSearchResults(query){
     fetch( "/api/v1/books/search?query=" + query).then(response => response.json()).then(data=> displaySearchResults(data))
}

function displaySearchResults(data){
     data.forEach(
         searchResult => console.log(searchResult.title)
     )
}