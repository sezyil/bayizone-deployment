export const toggleMobileMenu = (close: boolean = false) => {
    let target = document.querySelector('.sidebar-expand-xl');
    if (target) {
        let isClosed = target.classList.contains('sidebar-mobile-expanded');
        if (close || isClosed) {
            target.classList.remove('sidebar-mobile-expanded');
        } else {
            target.classList.add('sidebar-mobile-expanded');
        }
    }
}