<article id="board_area">
<header>
	<h1></h1>
</header>

<!--<form class="form-horizontal" method="post" action="" id="write_action">-->

<form method="post" class="form-horizontal" id="upload_action" enctype="multipart/form-data" action="/bbs/controlls/upload_file/upload_files">
<?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
	<fieldset>
		<legend>이미지 업로드</legend>
		<div class="control-group">
			<!-- 파일 업로드 -->
			<label class="control-label" for="input01">제목</label>
			<div class="controls">
				<input type="file" class="input-xlarge" id="input01" name="upload_file" value="<?php echo set_value('filename'); ?>">
				<p class="help-block">파일을 선택해주세요.</p>
			</div>
			<!-- 파일 업로드 -->
			<label class="control-label" for="input02">제목</label>
			<div class="controls">
				<input type="text" class="input-xlarge" id="input02" name="subject" value="<?php echo set_value('subject'); ?>">
				<p class="help-block">게시물의 제목을 써주세요.</p>
			</div>
			<label class="control-label" for="input03">내용</label>
			<div class="controls">
				<textarea class="input-xlarge" id="input03" name="contents" rows="5"><?php echo set_value('contents'); ?></textarea>
				<p class="help-block">게시물의 내용을 써주세요.</p>
			</div>

		<div class="controls">
				<p class="help-block"><?php echo validation_errors(); ?></p>
			</div>

			<div class="form-actions">
				<button type="submit" class="btn btn-primary" id="write_btn">이미지 업로드</button>
				<button type="button" class="btn" onclick="window.location.href='/bbs/board/lists/ci_board/page/1'">취소</button>
			</div>
		</div>
	</fieldset>
</form>
</article>