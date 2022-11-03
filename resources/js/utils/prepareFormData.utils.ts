// @ts-nocheck
export default (data) => {
    const formData = new FormData();
    for (const [key, value] of Object.entries(data)) {
        if (key !== "files") formData.append(key, value);
    }

    for (let i = 1; i <= data.files.length; i++) {
        const file = data.files[i - 1];
        formData.append(`file_${i}`, file);
    }

    return formData;
}
