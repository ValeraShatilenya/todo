export default (file: string, fileName: string): void => {
    const downloadUrl = window.URL.createObjectURL(new window.Blob([file]));
    const link = document.createElement("a");
    link.href = downloadUrl;
    link.setAttribute("download", fileName);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
