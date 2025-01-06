function showPopup(element) {
    const popup = element.querySelector('.popup-details');
    if (popup) {
        popup.style.display = 'block';
    }
}

function hidePopup(element) {
    const popup = element.querySelector('.popup-details');
    if (popup) {
        popup.style.display = 'none';
    }
}

function onHandleChangeFloor(slide){
    const floor = document.querySelector('.row');
    const floorInput = document.querySelector('select[name="search_room_form[floor]"]');
    let values = Array.from(floorInput.options).map((option) => option.value);
    let currentValue=parseInt(floorInput.value) || 0;

    if (slide === 'slide_down') {
        if(currentValue>Math.min(...values)){
            floor.classList.add('slide_down');
            currentValue--;
        }
    }
    if (slide === 'slide_up') {
        if(currentValue<values.at(3)){
            floor.classList.add('slide_up');
            currentValue++;
        }
    }
    floorInput.value=currentValue
    floorInput.closest('form').submit();
}

window.showPopup = showPopup;
window.hidePopup = hidePopup;
window.onHandleChangeFloor = onHandleChangeFloor;