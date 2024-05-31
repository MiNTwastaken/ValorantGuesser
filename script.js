const dropdownBtns = document.querySelectorAll('.dropdown-btn');
const dropdownContents = document.querySelectorAll('.dropdown-content');

dropdownBtns.forEach((btn, index) => {
    btn.addEventListener('mouseover', () => {
        dropdownContents[index].style.display = 'block';
    });

    btn.addEventListener('mouseout', () => {
        dropdownContents[index].style.display = 'none';
    });
});