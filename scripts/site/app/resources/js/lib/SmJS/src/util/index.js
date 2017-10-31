/**
 * Convert a Map into an object.
 *
 * @param strMap
 * @return {Object}
 */
export const mapToObj = strMap => {
    let obj = Object.create(null);
    for (let [k, v] of strMap) {obj[k] = v;}
    return obj;
};

export default {mapToObj}