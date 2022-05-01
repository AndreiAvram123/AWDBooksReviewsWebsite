
let abortController = new AbortController();
let signal = abortController.signal
let containerBookResults = document.getElementById("container-book-results");

let selectedBookFormField = document.getElementById("selected-book-form-field");
let lastSelectedSearchItem = undefined;


//todo

//create actual class for search ui item


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
                console.log(error)
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
    let searchUIItem = document.createElement('div');


    let span = document.createElement('span')
    span.innerText = dataItem.title
    searchUIItem.appendChild(span);
    attachListenerToSearchUIItem(
        searchUIItem,
        dataItem
    )
    debugger
    console.log("sdfsdf")
    if(dataItem.googleBookID === undefined){
       let exclusiveUIElement =   document.createElement('span')
        exclusiveUIElement.innerText ="Exclusive book"
        exclusiveUIElement.className = 'ml-5'
        searchUIItem.appendChild(exclusiveUIElement)
        debugger
    }
    return searchUIItem;
}

function attachListenerToSearchUIItem(
    searchUiItem,
    dataItem
){
    searchUiItem.addEventListener('click',()=>{
        unHighlightLastSearchItem()
        lastSelectedSearchItem = searchUiItem;
         highlightLastSearchItem()
        if(dataItem.bookID !== 0){
           selectedBookFormField.value = "book_id_" + dataItem.bookID;
        }else{
            selectedBookFormField.value = "google_book_id_" + dataItem.googleBookID;
        }
    })
}

function highlightLastSearchItem(){
    if(lastSelectedSearchItem !== undefined){
        lastSelectedSearchItem.className = "search-item-highlight"
    }
}
function unHighlightLastSearchItem(){
    if(lastSelectedSearchItem !== undefined){
        lastSelectedSearchItem.className = "search-item"
    }
}
