export function getCsrfFields() {
    try {
        const csrfNameField  = document.querySelector('#csrfName')
        const csrfValueField = document.querySelector('#csrfValue')
        const csrfNameKey    = csrfNameField.getAttribute('name')
        const csrfName       = csrfNameField.content
        const csrfValueKey   = csrfValueField.getAttribute('name')
        const csrfValue      = csrfValueField.content

        return {
            [csrfNameKey]: csrfName,
            [csrfValueKey]: csrfValue
        }
    } catch (e) {
        return [];
    }
}