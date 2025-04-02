// parte menu
window.addEventListener("scroll", function(){
    let header = this.document.querySelector('#header')
    header.classList.toggle('rolagem',window.scrollY > 300)
})

// fim parte menu

// PARTE IFRAME

document.getElementById('open-iframe-btn').onclick = function() {
    document.getElementById('iframeContainer').style.display = 'flex';
};

document.getElementById('closeBtn').onclick = function() {
    document.getElementById('iframeContainer').style.display = 'none';
};

// FIM PARTE IFRAME


// parte carrossel

const carrossel = document.querySelector('.carrossel');
const backBtn = document.getElementById('backBtn');
const nextBtn = document.getElementById('nextBtn');

let scrollAmount = 0;

nextBtn.addEventListener('click', () => {
    if (scrollAmount < carrossel.scrollWidth - carrossel.clientWidth) {
        scrollAmount += carrossel.clientWidth / 3;
        carrossel.scrollTo({
            top: 0,
            left: scrollAmount,
            behavior: 'smooth'
        });
    }
});

backBtn.addEventListener('click', () => {
    if (scrollAmount > 0) {
        scrollAmount -= carrossel.clientWidth / 3;
        carrossel.scrollTo({
            top: 0,
            left: scrollAmount,
            behavior: 'smooth'
        });
    }
});