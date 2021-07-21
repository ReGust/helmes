let character = String.fromCharCode(160);
const tab = (character + character + character + character)
let options = document.querySelectorAll('#sectors option');
let processedData = [];
let parentElement = 0;
let Lvl2ParentElement = 0;
let Lvl3ParentElement = 0;

jQuery(document).ready(function() {
    options.forEach( e => {
        processedData = prepareDataForDatabase(e.text, e.value, processedData)
    })
    insertDataIntoDB(processedData)
})

function insertDataIntoDB(data)
{
    jQuery.ajax({
        type: "POST",
        url: 'form-post.php',
        data: {
            action: "saveSectorTree",
            data: JSON.stringify(data)
        },
        success: function (data) {
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(jqXHR + " :: " + textStatus + " :: " + errorThrown)
        }
    });
}

// create sectors tree data structure
function prepareDataForDatabase(string, value, processedData)
{
    let i = 0;
    if (string.includes((tab+tab+tab))) {
        let node = {
            'id_sector' : value,
            'name' : string,
            'parent' : Lvl3ParentElement
        }
        processedData.push(node)
    } else if (string.includes((tab+tab))) {
        let node = {
            'id_sector' : value,
            'name' : string,
            'parent' : Lvl2ParentElement
        }
        processedData.push(node)
        Lvl3ParentElement = value;
    } else if (string.includes(tab)){
        let node = {
            'id_sector' : value,
            'name' : string,
            'parent' : parentElement
        }
        processedData.push(node)
        Lvl2ParentElement = value;
    } else {
        let node = {
            'id_sector' : value,
            'name' : string,
            'parent' : 0
        }
        processedData.push(node)
        parentElement = value;
    }
    return processedData;
}
