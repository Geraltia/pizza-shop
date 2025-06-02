<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $uuid
 * @property string $status
 * @property string|null $file_path
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereUuid($value)
 * @mixin \Eloquent
 */
final class Report extends Model
{
    protected $fillable = [
        'uuid', 'status', 'file_path',
    ];
}
