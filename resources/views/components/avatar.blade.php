@props(['user', 'size' => 'md'])
<span {{ $attributes->class(['tr-avatar', 'tr-avatar--'.$size, 'tr-avatar--c'.($user->id % 3)]) }} aria-hidden="true">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
