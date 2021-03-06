

class Section{

    constructor(sectionNumber, currentTitle, currentSummary) {
        this.sectionNumber = sectionNumber;
        this.currentTitle = currentTitle;
        this.currentSummary = currentSummary;
        this.createContainer()
        this.container.appendChild(this.createHeadingLabel());
        this.container.appendChild(this.createSectionTitleInput())
        this.container.appendChild(this.createSummaryInput())
    }

    createContainer(){
        let container = document.createElement('div');
        container.className = "form-group";
        this.container = container;
    }

    createHeadingLabel(){
        let label = document.createElement('label');
        label.className = "form-label required";
        label.innerText = "Section" + this.sectionNumber;
        return label;
    }

    createSectionTitleInput(){
        let sectionTitle = document.createElement('input');
        sectionTitle.name = "section_" + this.sectionNumber + "_title";
        sectionTitle.required = true;
        sectionTitle.className = "form-control"
        sectionTitle.placeholder = "Enter  section " + this.sectionNumber +  " title";
        sectionTitle.value = this.currentTitle;
        return sectionTitle;
    }

    createSummaryInput(){
        let textArea = document.createElement('textarea');
        textArea.id = "section_" + this.sectionNumber + "_summary";
        textArea.name= "section_" + this.sectionNumber + "_summary";
        textArea.required = true;
        textArea.placeholder ="Enter section " + this.sectionNumber +  " summary";
        textArea.className = "form-control mt-2"
        textArea.value = this.currentSummary;
        return  textArea;
    }
}


let container = document.getElementById('container-sections')
let numberSectionsInput = document.getElementById('book_review_number_sections')


function prePopulate(){
   if(sectionsData !== undefined){
       sectionsData.forEach((section,index) => {
           let sectionObject = new Section(index + 1,section.heading,section.text);
            container.appendChild(sectionObject.container);
       })
   }
}

function attachListener(){
    numberSectionsInput.addEventListener('input',(event)=> {
        onNumberSectionsChanged(event.target.value)
    })

}


function onNumberSectionsChanged(numberSections){
    container.innerHTML = "";
    for (let sectionNumber = 1; sectionNumber <= numberSections; sectionNumber++ ) {
        let section = new Section(sectionNumber,"","");
        container.appendChild(section.container)
    }
}