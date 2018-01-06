<article id="board_area">
		<header>
			<h1></h1>
		</header>
	<form method="post" class="form-horizontal" id="user_register" action="">
	<?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
		  <fieldset>
		    <legend>회원가입</legend>
		    <div class="control-group">
		      <label class="control-label" for="input01">아이디</label>
		      <div class="controls">
		        <input type="text" class="input-xlarge" id="input01" name="username" value="<?php echo set_value('username'); ?>" placeholder="아이디를 입력해주세요.">
		        <p class="help-block"></p>
		      </div>
		      <label class="control-label" for="input02">비밀번호</label>
		      <div class="controls">
		        <input type="password" class="input-xlarge" id="input02" name="password" value="<?php echo set_value('password'); ?>" placeholder="패스워드를 입력해주세요.">
		        <p class="help-block"></p>
		      </div>
					<label class="control-label" for="input02">비밀번호 확인</label>
		      <div class="controls">
						<input type="password" class="input-xlarge" id="input03" name="re_password" value="<?php echo set_value('re_password'); ?>" placeholder="패스워드를 입력해주세요.">
		        <p class="help-block"></p>
		      </div>
              <label class="control-label" for="input03">이름</label>
		      <div class="controls">
		        <input type="text" class="input-xlarge" id="input04" name="nickname" value="<?php echo set_value('nickname'); ?>" placeholder="이름을 입력해주세요.">
		        <p class="help-block"></p>
		      </div>
              <label class="control-label" for="input04">이메일</label>
		      <div class="controls">
		        <input type="text" class="input-xlarge" id="input05" name="email" value="<?php echo set_value('email'); ?>" placeholder="이메일을 입력해주세요.">
		        <p class="help-block"></p>
		      </div>

			  <div class="controls">
		        <p class="help-block"><?php echo validation_errors(); ?></p>
		      </div>

		      <div class="form-actions">
		        <button type="submit" class="btn btn-primary">회원가입</button>
		        <button type="button" class="btn" onclick="window.location.href='/bbs/auth/login'">취소</button>
		      </div>
		    </div>
		  </fieldset>
	</form>
	</article>
