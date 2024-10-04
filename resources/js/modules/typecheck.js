const isDefined = (element) => {
    return typeof element !== 'undefined' && element !== null && element !== '';
}
const isEmptyObj = (obj) => {
    return obj.constructor === Object
        && Object.keys(obj).length === 0;


}
export {
    isDefined,
    isEmptyObj
}
