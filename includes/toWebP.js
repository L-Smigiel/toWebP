document.getElementById('upload-form').addEventListener('submit', function(event) {
    var shouldConvert = confirm("Would you like to convert the file/files to WebP?");
    if (shouldConvert) {
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'should_convert_to_webp';
        input.value = 'yes';
        event.target.appendChild(input);
    }
});