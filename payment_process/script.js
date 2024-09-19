function selectCOD() {
    // Get the COD option element
    var codOption = document.getElementById("cod-option");

    // Toggle the selected state
    codOption.classList.toggle("bg-green-500");
    codOption.classList.toggle("text-white");
    
    // Show the confirm button container
    var confirmBtnContainer = document.getElementById("confirm-btn-container");
    confirmBtnContainer.classList.toggle("hidden");
}
