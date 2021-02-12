import Vue from 'vue'
import Toasted from 'vue-toasted'

import 'vue-toasted/dist/vue-toasted.min.css';

Vue.use(Toasted)

export const toastedMessages = {
    methods: {
        toastedErrorMessage(message) {
            Vue.toasted.error(message, {
                theme: 'primary',
                position: 'bottom-center',
                fullWidth: true,
                fitToScreen: true,
                className: 'toast-danger',
            }).goAway(5000)
        },
        toastedSuccessMessage(message) {
            Vue.toasted.success(message, {
                theme: 'primary',
                position: 'bottom-center',
                fullWidth: true,
                fitToScreen: true,
                className: 'toast-success',
            }).goAway(5000)
        },
        toastedWarningMessage(message) {
            Vue.toasted.success(message, {
                theme: 'primary',
                position: 'bottom-center',
                fullWidth: true,
                fitToScreen: true,
                className: 'toast-warning',
            }).goAway(5000)
        },
        toastedSubtleStatusMessage(message) {
            Vue.toasted.success(message, {
                theme: 'primary',
                position: 'bottom-right',
                fullWidth: false,
                fitToScreen: false,
                className: 'toast-status',
            }).goAway(2000)
        },
        toastedSuccessWithLink(message, url, linkText) {
            Vue.toasted.success(message, {
                theme: 'primary',
                position: 'bottom-center',
                fullWidth: true,
                fitToScreen: true,
                className: 'toast-success',
                action: {
                    class: 'toasted__link',
                    text: linkText,
                    onClick: () => {
                        window.location = url
                    },
                },
            }).goAway(20000)
        },
        toastedErrorWithLink(message, url, linkText) {
            Vue.toasted.error(message, {
                theme: 'primary',
                position: 'bottom-center',
                fullWidth: true,
                fitToScreen: true,
                className: 'toast-danger',
                action: {
                    class: 'toasted__link',
                    text: linkText,
                    onClick: () => {
                        window.location = url
                    },
                },
            }).goAway(20000)
        },
    },
}
