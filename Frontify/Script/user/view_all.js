const categorySelect = document.getElementById('category-select');
const searchInput = document.getElementById('search-input');
const searchBtn = document.getElementById('search-btn');
const blogPosts = document.querySelectorAll('.blog-post');

// Filter blog posts based on category selection
categorySelect.addEventListener('change', () => {
  const selectedCategory = categorySelect.value;
  blogPosts.forEach(post => {
    if (selectedCategory === 'all' || post.querySelector('.category').textContent === selectedCategory) {
      post.style.display = 'block';
    } else {
      post.style.display = 'none';
    }
  });
});

// Filter blog posts based on search input
searchBtn.addEventListener('click', () => {
  const searchTerm = searchInput.value.toLowerCase();
  blogPosts.forEach(post => {
    const blogTitle = post.querySelector('.blog-title').textContent.toLowerCase();
    const blogContent = post.querySelector('.blog-content').textContent.toLowerCase();
    if (blogTitle.includes(searchTerm) || blogContent.includes(searchTerm)) {
      post.style.display = 'block';
    } else {
      post.style.display = 'none';
    }
  });
});


//****************************  like ************************************************** */
document.querySelectorAll('.like-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const postId = btn.dataset.id;
        fetch('like_post.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ post_id: postId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                btn.querySelector('.like-count').textContent = data.likes;
            }
        });
    });
});


/********************************************** comment pop up */
document.querySelectorAll('.comment-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const postId = btn.dataset.id;
        fetch(`fetch_comments.php?post_id=${postId}`)
            .then(response => response.json())
            .then(comments => {
                const popup = document.createElement('div');
                popup.classList.add('comment-popup');
                popup.innerHTML = `<h3>Comments</h3><div>${comments.map(c => `<p><b>${c.uname}</b>: ${c.comment}</p>`).join('')}</div>`;
                document.body.appendChild(popup);
            });
    });
});
