<?php

declare(strict_types=1);

namespace App\Classes;

interface VoterPermissions
{
    public const string VIEW = 'view';

    public const string EDIT = 'edit';

    public const string DELETE = 'delete';

    public const string CREATE = 'create';

    public const string UNLOCK = 'unlock';

    public const string LOCK = 'lock';

    public const string ARCHIVE = 'archive';

    public const string ROLLOVER = 'rollover';

    public const string CREATE_TEMPORARY_FILE = 'create_temporary_file';

    public const string VIEW_VIRTUAL_LINK = 'view_virtual_link';

    public const string VIEW_DRAFT_CONTENTS = 'view_draft_contents';
}
