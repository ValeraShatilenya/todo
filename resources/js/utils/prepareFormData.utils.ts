// @ts-nocheck
export default (data) => {
    const formData = new FormData();
    for (const [key, value] of Object.entries(data)) {
        if(key === "files") continue;
        if(!Array.isArray(value)) formData.append(key, value);
        else {
            value.forEach(item => {
                formData.append(`${key}[]`, item);
            });
        }
    }

    data.files.forEach((file) => {
        formData.append('files[]', file);
    });

    return formData;
}
