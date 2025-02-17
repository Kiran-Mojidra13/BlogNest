<?php
    @include '../../../DataForge/UserData/insert_data.php';
    @include '../../../DataForge/UserData/index_Set.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rich Text Editor</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f4f4f4;
    }

    .editor-container {
        display: flex;
        flex-wrap: wrap;
        max-width: 100%;
        margin: 20px auto;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        height: 600px;
    }

    .editor-main {
        flex: 3;
        padding: 20px;
        min-width: 300px;
    }

    .toolbar {
        display: flex;
        align-items: center;
        padding: 10px;
        border-bottom: 1px solid #ddd;
        background: #f9f9f9;
        flex-wrap: wrap;
    }

    .toolbar button,
    .toolbar select {
        background: #fff;
        border: 1px solid #ddd;
        padding: 5px 10px;
        margin-right: 5px;
        cursor: pointer;
        border-radius: 3px;
    }

    .toolbar button:hover,
    .toolbar select:hover {
        background: #e9ecef;
    }

    .editor-content {
        border: 1px solid #ddd;
        padding: 10px;
        height: 400px;
        /* Fixed height */
        max-height: 500px;
        /* Prevent resizing beyond this height */
        border-radius: 3px;
        background: #fff;
        outline: none;
        margin-top: 20px;
        overflow-y: auto;
        /* Scrollbar for overflowing content */
        overflow-x: hidden;
        /* Hide horizontal scrolling */
    }




    #title-input {
        font-size: 1.5rem;
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        margin-bottom: 10px;
        border-radius: 3px;
        background: #fff;
    }

    .dropdown {
        position: relative;
        display: inline-block;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        z-index: 10;
    }

    .dropdown:hover .dropdown-content {
        display: block;
    }

    .dropdown-content button {
        background: #fff;
        border: none;
        padding: 5px 10px;
        text-align: left;
        cursor: pointer;
        display: block;
        width: 100%;
    }

    .dropdown-content button:hover {
        background: #e9ecef;
    }

    .emoji-picker {
        display: none;
        position: absolute;
        z-index: 10;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 10px;
        max-width: 300px;
        max-height: 200px;
        overflow-y: auto;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        margin-left: 630px;
        margin-top: 120px;
    }

    .emoji-picker span {
        font-size: 1.5rem;
        cursor: pointer;
        margin: 5px;
    }

    .emoji-picker span:hover {
        background: #f4f4f4;
        border-radius: 5px;
    }

    .settings-bar {
        flex: 1;
        padding: 20px;
        border-left: 1px solid #ddd;
        background: #f9f9f9;
        min-width: 250px;
    }

    .settings-bar h4 {
        font-size: 1.2rem;
        margin-bottom: 10px;
    }

    .settings-bar select,
    .settings-bar input[type="date"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 3px;
        margin-bottom: 15px;
        background: #fff;
    }

    .settings-bar label {
        font-weight: bold;
        margin-bottom: 5px;
        display: block;
    }

    @media screen and (max-width: 900px) {
        .editor-container {
            flex-direction: column;
        }

        .settings-bar {
            border-left: none;
            border-top: 1px solid #ddd;
        }
    }


    /* button hover code --------------------------------------------------------------------------------------------------------*/
    .buttons-container {
        margin-top: auto;
        display: flex;
        justify-content: center;
    }

    button {
        background: white;
        border: solid 2px black;
        padding: .375em 1.125em;
        font-size: 1rem;
    }

    .button-arounder {
        font-size: 1rem;
        background: hsl(190, 37%, 19%);
        color: hsl(190deg, 10%, 95%);
        box-shadow: 0 0px 0px hsla(190deg, 15%, 5%, .2);
        transform: translateY(0);
        border-radius: 0px;
        --dur: .15s;
        --delay: .15s;
        --radius: 16px;

        transition:
            border-radius var(--dur) var(--delay) ease-out,
            box-shadow calc(var(--dur) * 4) ease-out,
            transform calc(var(--dur) * 4) ease-out,
            background calc(var(--dur) * 4) steps(4, jump-end);
    }

    .button-arounder:hover,
    .button-arounder:focus {
        box-shadow: 0 4px 8px hsla(190deg, 15%, 5%, .2);
        transform: translateY(-4px);
        background: hsl(199, 88%, 16%);
        border-radius: var(--radius);
    }

    /* changes done by me*/
    #editor {
        border: 1px solid #ddd;
        padding: 10px;
        min-height: 300px;
        border-radius: 3px;
        background: #fff;
        margin-top: 10px;
        overflow-y: auto;
    }

    /* back arrow ---------------------------------------------------------------------------------------*/
    .header_back {
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
    }

    .header_back a {
        color: #000;
        text-decoration: none;
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .header_back a:hover {
        color: #0a417b;
        /* Highlight color on hover */
    }

    /* Header Styles ....................................................................................................................*/
    /* Header Styles */
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 20px;
        background-color: #f8f9fa;
        border-bottom: 1px solid #ddd;
        position: relative;
        z-index: 1000;
    }

    /* Back Arrow on the Left */
    .header_back {
        display: flex;
        align-items: center;
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
    }

    .header_back a {
        color: #000;
        text-decoration: none;
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .header_back a:hover {
        color: #0a417b;
    }

    /* Profile Section */
    .header_profile {
        display: flex;
        align-items: center;
        gap: 10px;
        /* Space between image and username */
        margin-left: auto;
        /* Push to the right */
    }

    .profile_image {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #ccc;
    }

    .username {
        font-size: 16px;
        font-weight: 600;
        color: #333;
    }
    </style>
    <style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #333;
        padding: 10px 20px;
        color: white;
    }

    .header .logo {
        display: flex;
        align-items: center;
    }

    .header .logo img {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        margin-right: 10px;
    }

    .header .logo span {
        font-size: 24px;
        font-weight: bold;
    }

    .header .menu {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .header .menu a {
        color: white;
        text-decoration: none;
        font-size: 16px;
    }

    .header .menu a:hover {
        text-decoration: underline;
    }

    .header .user-info {
        display: flex;
        align-items: center;
        cursor: pointer;
    }

    .header .user-info img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: 10px;
    }

    .header .user-info span {
        font-size: 16px;
    }
    </style>
    <header>
        <div class="header">
            <!-- Logo Section -->
            <div class="logo">
                <img src="../../../Frontify/Images/BlogNest_LOGO.png" alt="Blognest Logo">
                <span>BLOGNEST</span>
            </div>

            <!-- Menu Section -->
            <div class="menu">
                <a href="MainIndex.php">Home</a>
                <a href="create_blog.php">Create New Blog</a>

                <a href="view_blog.php">My Blog</a>
                <a href="../../../DataForge/UserData/logout.php">Logout</a>
            </div>

            <!-- User Info Section -->
            <div class="user-info" onclick="location.href='setting.php'">
                <img src="<?php echo htmlspecialchars($profileImage); ?>" alt="User Profile">
                <span><?php echo htmlspecialchars($username); ?></span>
            </div>
        </div>
    </header>

<body>

    <form action="" method="POST">
        <div class="editor-container">
            <!-- Main Editor Section -->
            <div class="editor-main"><input type="text" id="title-input" id="blog-title-input"
                    placeholder="Enter your blog title here...">
                <div class="toolbar"><button type="button" title="Undo"><i class="fas fa-undo"></i></button><button
                        type="button" title="Redo"><i class="fas fa-redo"></i></button><select title="Font Family"
                        onchange="changeFontFamily(this.value)">
                        <option value="Arial">Arial</option>
                        <option value="Georgia">Georgia</option>
                        <option value="Times New Roman">Times New Roman</option>
                        <option value="Verdana">Verdana</option>
                    </select><select title="Font Size" onchange="changeFontSize(this.value)">
                        <option value="1">Small</option>
                        <option value="3" selected>Normal</option>
                        <option value="5">Large</option>
                        <option value="7">Extra Large</option>
                    </select><button title="Bold"><i class="fas fa-bold"></i></button><button title="Italic"><i
                            class="fas fa-italic"></i></button><button title="Underline"><i
                            class="fas fa-underline"></i></button><button title="Text Color"><i
                            class="fas fa-paint-brush"></i></button><button title="Background Color"><i
                            class="fas fa-fill-drip"></i></button>
                    <div class="dropdown"><button type="button" title="Image Upload"><i
                                class="fas fa-photo-video"></i></button>
                        <div class="dropdown-content"><button type="button" onclick="triggerFileUpload()">Upload
                                from Computer</button><button type="button" onclick="triggerUrlInput()">Paste Image
                                URL</button></div>
                    </div><input type="file" id="file-upload" accept="image/*" style="display: none;"
                        onchange="uploadImage(this)">
                    <div class="dropdown"><button type="button" title="Video Upload"><i
                                class="fas fa-video"></i></button>
                        <div class="dropdown-content"><button type="button" onclick="triggerVideoUpload()">Upload
                                from Computer</button><button type="button" onclick="triggerYoutubeInput()">Embed
                                from YouTube</button></div>
                    </div><input type="file" id="video-upload" accept="video/*" style="display: none;"
                        onchange="uploadVideo(this)"><button type="button" onclick="openEmojiPicker()"
                        title="Insert Emoji"><i class="fas fa-smile"></i></button>
                    <div class="emoji-picker" id="emoji-picker"><span onclick="insertEmoji('😊')">😊</span><span
                            onclick="insertEmoji('😂')">😂</span><span onclick="insertEmoji('😍')">😍</span><span
                            onclick="insertEmoji('👍')">👍</span><span onclick="insertEmoji('🎉')">🎉</span><span
                            onclick="insertEmoji('😎')">😎</span><span onclick="insertEmoji('🥳')">🥳</span><span
                            onclick="insertEmoji('🤔')">🤔</span><span onclick="insertEmoji('🙌')">🙌</span><span
                            onclick="insertEmoji('❤️')">❤️</span></div>
                </div>
                <div id="editor" class="editor-content" contenteditable="true"></div>
            </div>
            <!-- Settings Bar -->
            <div class="settings-bar">
                <h4>Post Settings</h4><label for="publish-date">Publish Date</label><input type="date"
                    id="publish-date"><label for="category">Category</label><select id="category">
                    <option value="select">--Select--</option>
                    <option value="politics">Politics</option>
                    <option value="business">Business</option>
                    <option value="fashion">Fashion</option>
                </select><label for="reader-comments">Options</label><select id="reader-comments">
                    <option value="select">--Select--</option>
                    <option value="allow">Allow Reader Comments</option>
                    <option value="dont-allow">Don’t Allow Reader Comments</option>
                </select>
                <div class="buttons-container"><input type="submit" class="button-arounder" id="publish-button"
                        value="Publish"></div>
            </div>
        </div>
    </form>
    <script src="../../Script/user/create.js"></script>
</body>

</html>