<div class="message-inner">
    @if (isset($laReplies) && count($laReplies) > 0)
        @foreach ($laReplies as $key1 => $replies)
            <div class="card msg-box @if ($replies->user_id == $creactedBy) align-left @else align-right @endif">
                <div class="footnote">
                    <div class="card preview-card" style="margin: 0">
                        {{ $replies->text }}
                    </div>
                </div>
                <div class="footer-card">
                    <span class="caption-medium">{{ date('h:i A', strtotime($replies->created_at)) }}</span>
                </div>
            </div>
            @if ($key1 + 1 == count($laReplies))
                <input type="radio" id="focus-bottom" style="height: 0;width: 0;">
            @endif
        @endforeach
    @endif
</div>
