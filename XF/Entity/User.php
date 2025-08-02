<?php

namespace lulzapps\ModDebuff\XF\Entity;

class User extends XFCP_User
{
	public function canIgnoreUser(\XF\Entity\User $user, &$error = null)
	{
        if (is_array($error)) $error = [];

		if (!$user->user_id || !$this->user_id)
		{
			return false;
		}

        if ($user->is_admin)
        {
            $errors['is_staff'] = 'Administrators cannot be ignored.';
            return false;
        }

        $finder = \XF::finder('XF:Moderator');
        $moderator = $finder->where('user_id', $user->user_id)->fetchOne();
        if ($moderator && $moderator->is_super_moderator)
        {
            $errors['is_staff'] = 'Super moderators cannot be ignored.';
            return false;
        }

        if ($user->user_id == $this->user_id)
		{
			$error = \XF::phraseDeferred('you_may_not_ignore_yourself');
			return false;
		}

		if ($this->user_state != 'valid')
		{
			return false;
		}

		if (!in_array($user->user_state, ['valid', 'email_confirm', 'email_confirm_edit', 'email_bounce']))
		{
			return false;
		}

        return true;
    }
}