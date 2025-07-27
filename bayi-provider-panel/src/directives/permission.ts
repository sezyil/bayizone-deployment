// CanDirective.ts
import { Directive, DirectiveBinding } from 'vue';
//TODO:
// Directive definition
const CanDirective: Directive = {
    mounted(el: HTMLElement, binding: DirectiveBinding) {
        const canAccess = binding.value; // true or false

        if (!canAccess) {
            el.style.display = 'none';
        }
    },
};

export default CanDirective;