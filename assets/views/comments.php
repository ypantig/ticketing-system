<section id="comments" class="comments">
  <h4><?php echo __( 'Notes', 'yp-ticketing-system' ); ?></h4>
  <?php if (have_comments()): ?>
    <div class="comment-list">
      <?php wp_list_comments([
        'style' => 'div',
        'short_ping' => true,
        'callback' => '\\YP\\Comments::wp_list_comments__callback',
      ]) ?>
    </div>

    <?php if (get_comment_pages_count() > 1 && get_option('page_comments')): ?>
      <nav>
        <ul class="pager">
          <?php if (get_previous_comments_link()): ?>
            <li class="previous"><?php previous_comments_link(__('&larr; Older comments', 'yp-ticketing-system')); ?></li>
          <?php endif; ?>
          <?php if (get_next_comments_link()): ?>
            <li class="next"><?php next_comments_link(__('Newer comments &rarr;', 'yp-ticketing-system')); ?></li>
          <?php endif; ?>
        </ul>
      </nav>
    <?php endif; ?>
  <?php else: ?>
    <p><?php echo __( 'No notes to show', 'yp-ticketing-system' ); ?></p>
  <?php endif; ?>

  <?php if (!comments_open() && get_comments_number() != '0' && post_type_supports(get_post_type(), 'comments')): ?>
    <div class="alert alert-warning">
      <?php echo __('Comments are closed.', 'yp-ticketing-system'); ?>
    </div>
  <?php endif; ?>

  <?php comment_form(); ?>
</section>
