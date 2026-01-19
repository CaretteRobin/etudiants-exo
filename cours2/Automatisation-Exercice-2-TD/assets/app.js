import './style.scss';

const modal = document.querySelector('#modal-wrapper');
const modalContent = document.querySelector('#modal-content');
const modalTitle = document.querySelector('#modal-title');
const closeModalButton = document.querySelector('#close-modal');

const openModal = () => {
    if (modal) {
        modal.classList.add('active');
    }
};

const closeModal = () => {
    if (!modal) {
        return;
    }

    modal.classList.remove('active');
    if (modalContent) {
        modalContent.innerHTML = '';
    }
};

const loadModalContent = (url, title) => {
    if (!modalContent || !modalTitle) {
        return;
    }

    fetch(url)
        .then((response) => response.text())
        .then((data) => {
            modalContent.innerHTML = data;
            modalTitle.textContent = title;
            openModal();
        })
        .catch((error) => {
            console.error(error);
        });
};

const init = () => {
    if (closeModalButton) {
        closeModalButton.addEventListener('click', closeModal);
    }

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeModal();
        }
    });

    const officesList = document.querySelector('.offices-list');
    if (officesList) {
        officesList.addEventListener('click', (event) => {
            const target = event.target;
            if (target instanceof HTMLElement && target.classList.contains('office-edit')) {
                const officeId = target.dataset.officeId;
                if (officeId) {
                    loadModalContent(`/office/${officeId}/edit`, 'Édition du bureau');
                }
            }
        });
    }
};

document.addEventListener('DOMContentLoaded', init);
