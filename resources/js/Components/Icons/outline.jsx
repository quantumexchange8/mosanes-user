// Extra icons
import { defineComponent } from 'vue'

export const EmptyCircleIcon = defineComponent({
    setup() {
        return () => (
            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    d="M20.3149 15.4442C20.7672 14.3522 21 13.1819 21 12C21 9.61305 20.0518 7.32387 18.364 5.63604C16.6761 3.94821 14.3869 3 12 3C9.61305 3 7.32387 3.94821 5.63604 5.63604C3.94821 7.32387 3 9.61305 3 12C3 13.1819 3.23279 14.3522 3.68508 15.4442C4.13738 16.5361 4.80031 17.5282 5.63604 18.364C6.47177 19.1997 7.46392 19.8626 8.55585 20.3149C9.64778 20.7672 10.8181 21 12 21C13.1819 21 14.3522 20.7672 15.4442 20.3149C16.5361 19.8626 17.5282 19.1997 18.364 18.364C19.1997 17.5282 19.8626 16.5361 20.3149 15.4442Z"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
            </svg>
        )
    },
})

export const Menu01Icon = defineComponent({
    setup() {
        return () => (
            <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M16.5 24H31.5M16.5 19H31.5M16.5 29H31.5" stroke="currentColor" stroke-width="1.25"
                      stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        )
    },
})

export const XIcon = defineComponent({
    setup() {
        return () => (
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M15 5L5 15M5 5L15 15" stroke="currentColor" stroke-width="1.25" stroke-linecap="round"
                      stroke-linejoin="round"/>
            </svg>
        )
    },
})

export const ChevronDownIcon = defineComponent({
    setup() {
        return () => (
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="1.25" stroke-linecap="round"
                      stroke-linejoin="round"/>
            </svg>
        )
    },
})

export const Edit01Icon = defineComponent({
    setup() {
        return () => (
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_1119_11579)">
                    <path d="M1.91671 12.0786C1.94734 11.8029 1.96265 11.6651 2.00436 11.5363C2.04136 11.422 2.09364 11.3132 2.15977 11.2129C2.23432 11.0999 2.33238 11.0018 2.5285 10.8057L11.3327 2.0015C12.0691 1.26512 13.263 1.26512 13.9994 2.0015C14.7357 2.73788 14.7357 3.93179 13.9994 4.66817L5.19517 13.4724C4.99905 13.6685 4.90099 13.7665 4.78794 13.8411C4.68765 13.9072 4.57888 13.9595 4.46458 13.9965C4.33575 14.0382 4.19792 14.0535 3.92226 14.0841L1.66602 14.3348L1.91671 12.0786Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </g>
                <defs>
                    <clipPath id="clip0_1119_11579">
                        <rect width="16" height="16" fill="currentColor" />
                    </clipPath>
                </defs>
            </svg>
        )
    },
})

export const CreditCardEdit01Icon = defineComponent({
    setup() {
        return () => (
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1.33398 6.66536H14.6673V5.46536C14.6673 4.71863 14.6673 4.34526 14.522 4.06005C14.3942 3.80916 14.1902 3.60519 13.9393 3.47736C13.6541 3.33203 13.2807 3.33203 12.534 3.33203H3.46732C2.72058 3.33203 2.34721 3.33203 2.062 3.47736C1.81111 3.60519 1.60714 3.80916 1.47931 4.06004C1.33398 4.34526 1.33398 4.71863 1.33398 5.46536V10.532C1.33398 11.2788 1.33398 11.6521 1.47931 11.9374C1.60714 12.1882 1.81111 12.3922 2.062 12.52C2.34721 12.6654 2.72058 12.6654 3.46732 12.6654H7.33398M9.66732 13.9987L11.0173 13.7287C11.135 13.7052 11.1939 13.6934 11.2487 13.6719C11.2975 13.6528 11.3438 13.628 11.3867 13.598C11.435 13.5643 11.4775 13.5219 11.5624 13.437L14.334 10.6654C14.7022 10.2972 14.7022 9.70022 14.334 9.33203C13.9658 8.96384 13.3688 8.96384 13.0007 9.33203L10.229 12.1037C10.1441 12.1885 10.1017 12.231 10.068 12.2793C10.038 12.3223 10.0133 12.3686 9.99415 12.4173C9.97263 12.4722 9.96086 12.531 9.93731 12.6487L9.66732 13.9987Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        )
    },
})

export const Download02Icon = defineComponent({
    setup() {
        return () => (
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M14 14H2M12 7.33333L8 11.3333M8 11.3333L4 7.33333M8 11.3333V2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        )
    },
})

export const BalanceAdjustmentIcon = defineComponent({
    setup() {
        return () => (
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M3.66602 5C3.66602 6.38083 6.65102 7.5 10.3327 7.5C14.0143 7.5 16.9993 6.38083 16.9993 5M3.66602 5C3.66602 3.61917 6.65102 2.5 10.3327 2.5C14.0143 2.5 16.9993 3.61917 16.9993 5M3.66602 5V10M16.9993 5V10M3.66602 10C3.66602 11.3808 6.65102 12.5 10.3327 12.5C10.731 12.5 11.1218 12.4867 11.501 12.4617M3.66602 10V15C3.66602 16.2717 6.19935 17.3217 9.47602 17.4792M15.6827 13.0085C15.8452 12.846 16.0381 12.7171 16.2505 12.6291C16.4628 12.5412 16.6904 12.4959 16.9202 12.4959C17.15 12.4959 17.3776 12.5412 17.5899 12.6291C17.8022 12.7171 17.9952 12.846 18.1577 13.0085C18.3202 13.171 18.4491 13.364 18.5371 13.5763C18.625 13.7886 18.6703 14.0162 18.6703 14.246C18.6703 14.4758 18.625 14.7034 18.5371 14.9157C18.4491 15.1281 18.3202 15.321 18.1577 15.4835L15.3327 18.3335H12.8327V15.8335L15.6827 13.0085Z" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        )
    },
})

export const CreditAdjustmentIcon = defineComponent({
    setup() {
        return () => (
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10.865 17.2217C10.1133 17.7758 8.87083 17.5025 8.60417 16.4025C8.5509 16.1827 8.44648 15.9785 8.29941 15.8067C8.15233 15.6348 7.96676 15.5001 7.75779 15.4135C7.54882 15.327 7.32236 15.291 7.09685 15.3084C6.87133 15.3259 6.65313 15.3964 6.46 15.5142C5.17417 16.2975 3.70167 14.8258 4.485 13.5392C4.60258 13.3461 4.67296 13.1281 4.6904 12.9027C4.70785 12.6773 4.67187 12.451 4.58539 12.2422C4.49892 12.0333 4.36438 11.8479 4.19273 11.7008C4.02107 11.5537 3.81714 11.4492 3.5975 11.3958C2.13417 11.0408 2.13417 8.95917 3.5975 8.60417C3.81733 8.5509 4.02148 8.44648 4.19333 8.29941C4.36518 8.15233 4.49988 7.96676 4.58645 7.75779C4.67303 7.54882 4.70904 7.32236 4.69156 7.09685C4.67407 6.87133 4.60359 6.65313 4.48583 6.46C3.7025 5.17417 5.17417 3.70167 6.46083 4.485C7.29417 4.99167 8.37417 4.54333 8.60417 3.5975C8.95917 2.13417 11.0408 2.13417 11.3958 3.5975C11.4491 3.81733 11.5535 4.02148 11.7006 4.19333C11.8477 4.36518 12.0332 4.49988 12.2422 4.58645C12.4512 4.67303 12.6776 4.70904 12.9032 4.69156C13.1287 4.67407 13.3469 4.60359 13.54 4.48583C14.8258 3.7025 16.2983 5.17417 15.515 6.46083C15.3327 6.7602 15.2661 7.11589 15.3278 7.46092C15.3895 7.80595 15.5753 8.11653 15.85 8.33417M12.5 10.0001C12.4998 9.45878 12.324 8.93216 11.9989 8.49937C11.6738 8.06657 11.2171 7.75097 10.6972 7.60001C10.1774 7.44905 9.62266 7.47087 9.11631 7.6622C8.60997 7.85354 8.1794 8.20404 7.88931 8.66104C7.59923 9.11804 7.46531 9.65684 7.50767 10.1965C7.55003 10.7361 7.76639 11.2474 8.12424 11.6535C8.48208 12.0597 8.96207 12.3387 9.49207 12.4487C10.0221 12.5586 10.5734 12.4936 11.0633 12.2634M17.5 12.5H15.4167C15.0851 12.5 14.7672 12.6317 14.5328 12.8661C14.2984 13.1005 14.1667 13.4185 14.1667 13.75C14.1667 14.0815 14.2984 14.3995 14.5328 14.6339C14.7672 14.8683 15.0851 15 15.4167 15H16.25C16.5815 15 16.8995 15.1317 17.1339 15.3661C17.3683 15.6005 17.5 15.9185 17.5 16.25C17.5 16.5815 17.3683 16.8995 17.1339 17.1339C16.8995 17.3683 16.5815 17.5 16.25 17.5H14.1667M15.8333 17.5V18.3333M15.8333 11.6667V12.5" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        )
    },
})

export const DeleteIcon = defineComponent({
    setup() {
        return () => (
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M3.33398 5.83333H16.6673M8.33398 9.16667V14.1667M11.6673 9.16667V14.1667M4.16732 5.83333L5.00065 15.8333C5.00065 16.2754 5.17625 16.6993 5.48881 17.0118C5.80137 17.3244 6.22529 17.5 6.66732 17.5H13.334C13.776 17.5 14.1999 17.3244 14.5125 17.0118C14.8251 16.6993 15.0007 16.2754 15.0007 15.8333L15.834 5.83333M7.50065 5.83333V3.33333C7.50065 3.11232 7.58845 2.90036 7.74473 2.74408C7.90101 2.5878 8.11297 2.5 8.33398 2.5H11.6673C11.8883 2.5 12.1003 2.5878 12.2566 2.74408C12.4129 2.90036 12.5007 3.11232 12.5007 3.33333V5.83333" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        )
    },
})

export const SearchIcon = defineComponent({
    setup() {
        return () => (
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M17.5 17.5L14.5834 14.5833M16.6667 9.58333C16.6667 13.4954 13.4954 16.6667 9.58333 16.6667C5.67132 16.6667 2.5 13.4954 2.5 9.58333C2.5 5.67132 5.67132 2.5 9.58333 2.5C13.4954 2.5 16.6667 5.67132 16.6667 9.58333Z" stroke="currentColor" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        )
    },
})

export const DownloadCloud01Icon = defineComponent({
    setup() {
        return () => (
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M3.33268 13.5352C2.32769 12.8625 1.66602 11.7168 1.66602 10.4167C1.66602 8.46369 3.15894 6.85941 5.0658 6.68281C5.45586 4.31011 7.51622 2.5 9.99935 2.5C12.4825 2.5 14.5428 4.31011 14.9329 6.68281C16.8398 6.85941 18.3327 8.46369 18.3327 10.4167C18.3327 11.7168 17.671 12.8625 16.666 13.5352M6.66602 14.1667L9.99935 17.5M9.99935 17.5L13.3327 14.1667M9.99935 17.5V10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        )
    },
})

export const Sliders02Icon = defineComponent({
    setup() {
        return () => (
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4.16667 17.5L4.16667 12.5M4.16667 12.5C5.08714 12.5 5.83333 11.7538 5.83333 10.8333C5.83333 9.91286 5.08714 9.16667 4.16667 9.16667C3.24619 9.16667 2.5 9.91286 2.5 10.8333C2.5 11.7538 3.24619 12.5 4.16667 12.5ZM4.16667 5.83333V2.5M10 17.5V12.5M10 5.83333V2.5M10 5.83333C9.07953 5.83333 8.33333 6.57953 8.33333 7.5C8.33333 8.42048 9.07953 9.16667 10 9.16667C10.9205 9.16667 11.6667 8.42048 11.6667 7.5C11.6667 6.57953 10.9205 5.83333 10 5.83333ZM15.8333 17.5V14.1667M15.8333 14.1667C16.7538 14.1667 17.5 13.4205 17.5 12.5C17.5 11.5795 16.7538 10.8333 15.8333 10.8333C14.9129 10.8333 14.1667 11.5795 14.1667 12.5C14.1667 13.4205 14.9129 14.1667 15.8333 14.1667ZM15.8333 7.5V2.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        )
    },
})

export const CalendarIcon = defineComponent({
    setup() {
        return () => (
            <svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M17.5 8.5319H2.5M13.3333 1.86523V5.19857M6.66667 1.86523V5.19857M6.5 18.5319H13.5C14.9001 18.5319 15.6002 18.5319 16.135 18.2594C16.6054 18.0197 16.9878 17.6373 17.2275 17.1669C17.5 16.6321 17.5 15.932 17.5 14.5319V7.5319C17.5 6.13177 17.5 5.4317 17.2275 4.89692C16.9878 4.42652 16.6054 4.04407 16.135 3.80438C15.6002 3.5319 14.9001 3.5319 13.5 3.5319H6.5C5.09987 3.5319 4.3998 3.5319 3.86502 3.80438C3.39462 4.04407 3.01217 4.42652 2.77248 4.89692C2.5 5.4317 2.5 6.13177 2.5 7.5319V14.5319C2.5 15.932 2.5 16.6321 2.77248 17.1669C3.01217 17.6373 3.39462 18.0197 3.86502 18.2594C4.3998 18.5319 5.09987 18.5319 6.5 18.5319Z" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        )
    },
})

export const SwitchHorizontal01Icon = defineComponent({
    setup() {
        return () => (
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M13.3346 11.3333H2.66797M2.66797 11.3333L5.33464 8.66667M2.66797 11.3333L5.33463 14M2.66797 4.66667H13.3346M13.3346 4.66667L10.668 2M13.3346 4.66667L10.668 7.33333" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        )
    },
})
