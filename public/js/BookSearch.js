
let abortController = new AbortController();
let signal = abortController.signal
let containerBookResults = document.getElementById("container-book-results");

function configureInputListener(field){
    field.addEventListener('input',(event)=>{
      fetchSearchResults(event.target.value)
    })

}

function fetchSearchResults(query) {
    if (query !== "") {
        fetch("/api/v1/books/search?query=" + query,
            {
                signal: signal
            }).then(response => response.json()).then(data => displaySearchResults(data))
            .catch((error) => {
                //do nothing
            })
    }else{
        clearSearchContainer()
    }
}

function clearSearchContainer(){
    containerBookResults.innerHTML = "";
}

function displaySearchResults(data){
    clearSearchContainer()
     data.forEach(
         dataItem => {
             containerBookResults.appendChild(
                 buildSearchUIItem(dataItem)
             );
         }
     )
}

function buildSearchUIItem(dataItem){
    let div = document.createElement('div');
    div.className = "search-item"
    let span = document.createElement('span')
    span.innerText = dataItem.title
    div.appendChild(span);
    return div;
}