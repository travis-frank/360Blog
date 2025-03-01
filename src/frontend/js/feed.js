document.addEventListener("DOMContentLoaded", function () {
    const filterDropdown = document.getElementById("category-filter");
    const posts = document.querySelectorAll(".post-item");

    filterDropdown.addEventListener("change", function () {
        const selectedCategory = filterDropdown.value;

        posts.forEach(post => {
            const postCategory = post.getAttribute("data-category"); // Get category from data attribute
            
            if (selectedCategory === "all" || postCategory === selectedCategory) {
                post.style.display = "block"; // Show matching posts
            } else {
                post.style.display = "none"; // Hide non-matching posts
            }
        });
    });
});
