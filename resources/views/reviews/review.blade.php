<div class="review-modal" id="reviewModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.9); z-index: 1000; align-items: center; justify-content: center;">
    <div class="review-form-container" style="background: var(--card); border-radius: 20px; padding: 2rem; max-width: 500px; width: 90%; border: 1px solid var(--border);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.5rem;">Write a Review</h3>
            <button class="close-modal" style="background: none; border: none; color: white; font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>
        
        <form action="#" method="POST">
            @csrf
            
            <div class="rating-input" style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem;">Your Rating</label>
                <div class="star-rating" style="display: flex; gap: 0.5rem;">
                    <i class="far fa-star" data-rating="1" style="font-size: 2rem; cursor: pointer; color: #ffc107;"></i>
                    <i class="far fa-star" data-rating="2" style="font-size: 2rem; cursor: pointer; color: #ffc107;"></i>
                    <i class="far fa-star" data-rating="3" style="font-size: 2rem; cursor: pointer; color: #ffc107;"></i>
                    <i class="far fa-star" data-rating="4" style="font-size: 2rem; cursor: pointer; color: #ffc107;"></i>
                    <i class="far fa-star" data-rating="5" style="font-size: 2rem; cursor: pointer; color: #ffc107;"></i>
                </div>
                <input type="hidden" name="rating" id="ratingValue" value="0">
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem;">Review Title</label>
                <input type="text" name="title" placeholder="Summarize your experience" style="width: 100%; background: var(--bg); border: 1px solid var(--border); color: white; padding: 0.75rem; border-radius: 8px;">
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem;">Your Review</label>
                <textarea name="content" rows="5" placeholder="What did you think about the movie?" style="width: 100%; background: var(--bg); border: 1px solid var(--border); color: white; padding: 0.75rem; border-radius: 8px;"></textarea>
            </div>
            
            <div style="display: flex; gap: 1rem;">
                <button type="submit" style="background: var(--grad-2); border: none; color: white; padding: 0.75rem 1.5rem; border-radius: 8px; cursor: pointer; flex: 1;">Submit Review</button>
                <button type="button" class="close-modal" style="background: transparent; border: 1px solid var(--border); color: white; padding: 0.75rem 1.5rem; border-radius: 8px; cursor: pointer;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<style>
.star-rating i:hover,
.star-rating i.active {
    font-weight: 900;
    font-family: 'Font Awesome 6 Free';
}

.star-rating i:hover ~ i,
.star-rating i.active ~ i {
    font-weight: 400;
    font-family: 'Font Awesome 6 Free';
}
</style>

<script>
// Star rating functionality
document.querySelectorAll('.star-rating i').forEach(star => {
    star.addEventListener('click', function() {
        const rating = this.dataset.rating;
        document.getElementById('ratingValue').value = rating;
        
        document.querySelectorAll('.star-rating i').forEach((s, index) => {
            if (index < rating) {
                s.classList.remove('far');
                s.classList.add('fas');
                s.classList.add('active');
            } else {
                s.classList.remove('fas');
                s.classList.add('far');
                s.classList.remove('active');
            }
        });
    });
});

// Modal functionality
document.querySelectorAll('.btn-write-review, .write-review-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('reviewModal').style.display = 'flex';
    });
});

document.querySelectorAll('.close-modal').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('reviewModal').style.display = 'none';
    });
});
</script>