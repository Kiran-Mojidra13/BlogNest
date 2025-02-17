
    function changeFontFamily(font) {
        document.execCommand('fontName', false, font);
    }

    function changeFontSize(size) {
        document.execCommand('fontSize', false, size);
    }

    function changeTextColor() {
        const color = prompt('Enter a text color (e.g., "red" or "#ff0000")');
        if (color) document.execCommand('foreColor', false, color);
    }

    function changeBackgroundColor() {
        const color = prompt('Enter a background color (e.g., "yellow" or "#ffff00")');
        if (color) document.execCommand('hiliteColor', false, color);
    }

    function triggerFileUpload() {
        document.getElementById('file-upload').click();
    }

    function uploadImage(input) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const imgTag =
                    `<img src="${e.target.result}" style="max-width: 100%; height: auto; resize: both; overflow: auto;" />`;
                document.execCommand('insertHTML', false, imgTag);
            };
            reader.readAsDataURL(file);
        }
    }



    function triggerUrlInput() {
        const url = prompt('Paste the image URL:');
        if (url) {
            const imgTag = `<img src="${url}" style="max-width: 100%; height: auto; resize: both; overflow: auto;" />`;
            document.execCommand('insertHTML', false, imgTag);
        }
    }


    function triggerVideoUpload() {
        document.getElementById('video-upload').click();
    }

    function uploadVideo(input) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const videoTag = `<video controls style="max-width: 100%; height: auto; resize: both; overflow: auto;">
                                <source src="${e.target.result}" type="${file.type}">
                              </video>`;
                document.execCommand('insertHTML', false, videoTag);
            };
            reader.readAsDataURL(file);
        }
    }


    function triggerYoutubeInput() {
        const url = prompt('Paste the YouTube video URL:');
        if (url) {
            const embedUrl = url.replace('watch?v=', 'embed/');
            const iframe =
                `<iframe style="max-width:100%;" width="560" height="315" src="${embedUrl}" frameborder="0" allowfullscreen></iframe>`;
            document.execCommand('insertHTML', false, iframe);
        }
    }

    function openEmojiPicker() {
        const picker = document.getElementById('emoji-picker');
        picker.style.display = picker.style.display === 'block' ? 'none' : 'block';
    }

    function insertEmoji(emoji) {
        const editor = document.querySelector('.editor-content');
        editor.focus();
        document.execCommand('insertText', false, emoji);
        openEmojiPicker();
    }

    document.addEventListener('click', function(event) {
        const picker = document.getElementById('emoji-picker');
        if (!picker.contains(event.target) && !event.target.closest('.toolbar')) {
            picker.style.display = 'none';
        }
    });
    

   
    


    //-----------------------------------------------------------------------------------------------
    
