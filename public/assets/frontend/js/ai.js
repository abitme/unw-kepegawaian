function previewImage() {
    const img = document.querySelector('#cover');
    const label = document.querySelector('.custom-file-label');
    const imgPreview = document.querySelector('.img-preview');

    // change url browse
    label.textContent = img.files[0].name;

    // change img preview
    const fileImage = new FileReader();
    fileImage.readAsDataURL(img.files[0])

    fileImage.onload = function(e) {
        imgPreview.src = e.target.result;
    }
}

const status = $(".flashdata").data("status");
const message = $(".flashdata").data("message");

if (status && message) {
  Swal.fire({
    icon: status.toLowerCase(),
    title: status,
    text: message,
  });
}