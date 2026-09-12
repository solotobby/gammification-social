<?php

namespace App\Services;

class PostQualityService
{
    /**
     * Minimum number of words required for a post to be considered substantive.
     */
    public const MIN_WORD_COUNT = 5;

    /**
     * Minimum number of substantive words (3+ characters) required.
     */
    public const MIN_SUBSTANTIVE_WORDS = 4;

    /**
     * Minimum total character count of real words.
     */
    public const MIN_CHAR_COUNT = 25;

    /**
     * Maximum acceptable ratio of short words (1-2 letters).
     * If 60% or more of words are 1-2 letters, the post fails.
     */
    public const MAX_SHORT_WORD_RATIO = 0.60;

    /**
     * Minimum ratio of unique words (to flag repetitive word spam).
     */
    public const MIN_UNIQUE_WORD_RATIO = 0.50;

    /**
     * Comprehensive catalog of common English verbs, auxiliary verbs, and predicates.
     * Used to verify that content contains an actionable clause or grammatical predicate.
     *
     * @var array<string, bool>
     */
    protected static array $verbCatalog = [
        // Auxiliary & Modal
        'is' => true, 'am' => true, 'are' => true, 'was' => true, 'were' => true,
        'be' => true, 'been' => true, 'being' => true, 'have' => true, 'has' => true,
        'had' => true, 'do' => true, 'does' => true, 'did' => true, 'can' => true,
        'could' => true, 'will' => true, 'would' => true, 'shall' => true, 'should' => true,
        'may' => true, 'might' => true, 'must' => true,
        // Common linking & perception verbs
        'become' => true, 'becomes' => true, 'became' => true, 'seem' => true, 'seems' => true,
        'seemed' => true, 'feel' => true, 'feels' => true, 'felt' => true, 'look' => true,
        'looks' => true, 'looked' => true, 'sound' => true, 'sounds' => true, 'sounded' => true,
        'remain' => true, 'remains' => true, 'appear' => true, 'appears' => true, 'appeared' => true,
        'stay' => true, 'stays' => true, 'stayed' => true,
        // High-frequency communication & action verbs
        'make' => true, 'makes' => true, 'made' => true, 'making' => true,
        'go' => true, 'goes' => true, 'went' => true, 'gone' => true, 'going' => true,
        'take' => true, 'takes' => true, 'took' => true, 'taken' => true, 'taking' => true,
        'come' => true, 'comes' => true, 'came' => true, 'coming' => true,
        'see' => true, 'sees' => true, 'saw' => true, 'seen' => true, 'seeing' => true,
        'know' => true, 'knows' => true, 'knew' => true, 'known' => true, 'knowing' => true,
        'get' => true, 'gets' => true, 'got' => true, 'gotten' => true, 'getting' => true,
        'give' => true, 'gives' => true, 'gave' => true, 'given' => true, 'giving' => true,
        'find' => true, 'finds' => true, 'found' => true, 'finding' => true,
        'think' => true, 'thinks' => true, 'thought' => true, 'thinking' => true,
        'tell' => true, 'tells' => true, 'told' => true, 'telling' => true,
        'say' => true, 'says' => true, 'said' => true, 'saying' => true,
        'ask' => true, 'asks' => true, 'asked' => true, 'asking' => true,
        'work' => true, 'works' => true, 'worked' => true, 'working' => true,
        'call' => true, 'calls' => true, 'called' => true, 'calling' => true,
        'try' => true, 'tries' => true, 'tried' => true, 'trying' => true,
        'need' => true, 'needs' => true, 'needed' => true, 'needing' => true,
        'want' => true, 'wants' => true, 'wanted' => true, 'wanting' => true,
        'help' => true, 'helps' => true, 'helped' => true, 'helping' => true,
        'use' => true, 'uses' => true, 'used' => true, 'using' => true,
        'show' => true, 'shows' => true, 'showed' => true, 'shown' => true, 'showing' => true,
        'start' => true, 'starts' => true, 'started' => true, 'starting' => true,
        'stop' => true, 'stops' => true, 'stopped' => true, 'stopping' => true,
        'keep' => true, 'keeps' => true, 'kept' => true, 'keeping' => true,
        'talk' => true, 'talks' => true, 'talked' => true, 'talking' => true,
        'turn' => true, 'turns' => true, 'turned' => true, 'turning' => true,
        'leave' => true, 'leaves' => true, 'left' => true, 'leaving' => true,
        'put' => true, 'puts' => true, 'putting' => true,
        'like' => true, 'likes' => true, 'liked' => true, 'liking' => true,
        'love' => true, 'loves' => true, 'loved' => true, 'loving' => true,
        'live' => true, 'lives' => true, 'lived' => true, 'living' => true,
        'read' => true, 'reads' => true, 'reading' => true,
        'write' => true, 'writes' => true, 'wrote' => true, 'written' => true, 'writing' => true,
        'buy' => true, 'buys' => true, 'bought' => true, 'buying' => true,
        'sell' => true, 'sells' => true, 'sold' => true, 'selling' => true,
        'pay' => true, 'pays' => true, 'paid' => true, 'paying' => true,
        'earn' => true, 'earns' => true, 'earned' => true, 'earning' => true,
        'learn' => true, 'learns' => true, 'learned' => true, 'learning' => true,
        'build' => true, 'builds' => true, 'built' => true, 'building' => true,
        'share' => true, 'shares' => true, 'shared' => true, 'sharing' => true,
        'post' => true, 'posts' => true, 'posted' => true, 'posting' => true,
        'join' => true, 'joins' => true, 'joined' => true, 'joining' => true,
        'boost' => true, 'boosts' => true, 'boosted' => true, 'boosting' => true,
        'check' => true, 'checks' => true, 'checked' => true, 'checking' => true,
        'watch' => true, 'watches' => true, 'watched' => true, 'watching' => true,
        'listen' => true, 'listens' => true, 'listened' => true, 'listening' => true,
        'create' => true, 'creates' => true, 'created' => true, 'creating' => true,
        'bring' => true, 'brings' => true, 'brought' => true, 'bringing' => true,
        'hold' => true, 'holds' => true, 'held' => true, 'holding' => true,
        'lead' => true, 'leads' => true, 'led' => true, 'leading' => true,
        'offer' => true, 'offers' => true, 'offered' => true, 'offering' => true,
        'expect' => true, 'expects' => true, 'expected' => true, 'expecting' => true,
        'consider' => true, 'considers' => true, 'considered' => true, 'considering' => true,
        'remember' => true, 'remembers' => true, 'remembered' => true, 'remembering' => true,
        'understand' => true, 'understands' => true, 'understood' => true, 'understanding' => true,
        'believe' => true, 'believes' => true, 'believed' => true, 'believing' => true,
        'enjoy' => true, 'enjoys' => true, 'enjoyed' => true, 'enjoying' => true,
        'celebrate' => true, 'celebrates' => true, 'celebrated' => true, 'celebrating' => true,
        'suggest' => true, 'suggests' => true, 'suggested' => true, 'suggesting' => true,
        'explain' => true, 'explains' => true, 'explained' => true, 'explaining' => true,
        'announce' => true, 'announces' => true, 'announced' => true, 'announcing' => true,
        'discover' => true, 'discovers' => true, 'discovered' => true, 'discovering' => true,
        'provide' => true, 'provides' => true, 'provided' => true, 'providing' => true,
        'include' => true, 'includes' => true, 'included' => true, 'including' => true,
        'allow' => true, 'allows' => true, 'allowed' => true, 'allowing' => true,
        'support' => true, 'supports' => true, 'supported' => true, 'supporting' => true,
        'win' => true, 'wins' => true, 'won' => true, 'winning' => true,
        'lose' => true, 'loses' => true, 'lost' => true, 'losing' => true,
        'grow' => true, 'grows' => true, 'grew' => true, 'grown' => true, 'growing' => true,
        'improve' => true, 'improves' => true, 'improved' => true, 'improving' => true,
        'reach' => true, 'reaches' => true, 'reached' => true, 'reaching' => true,
        'spend' => true, 'spends' => true, 'spent' => true, 'spending' => true,
        'send' => true, 'sends' => true, 'sent' => true, 'sending' => true,
        'receive' => true, 'receives' => true, 'received' => true, 'receiving' => true,
        'plan' => true, 'plans' => true, 'planned' => true, 'planning' => true,
        'mean' => true, 'means' => true, 'meant' => true, 'meaning' => true,
        'change' => true, 'changes' => true, 'changed' => true, 'changing' => true,
        'connect' => true, 'connects' => true, 'connected' => true, 'connecting' => true,
        'launch' => true, 'launches' => true, 'launched' => true, 'launching' => true,
        'deliver' => true, 'delivers' => true, 'delivered' => true, 'delivering' => true,
        'discuss' => true, 'discusses' => true, 'discussed' => true, 'discussing' => true,
        'solve' => true, 'solves' => true, 'solved' => true, 'solving' => true,
        'protect' => true, 'protects' => true, 'protected' => true, 'protecting' => true,
        'manage' => true, 'manages' => true, 'managed' => true, 'managing' => true,
    ];

    /**
     * Common pure prepositions and conjunctions.
     *
     * @var array<string, bool>
     */
    protected static array $functionWords = [
        'and' => true, 'or' => true, 'but' => true, 'nor' => true, 'so' => true,
        'for' => true, 'yet' => true, 'in' => true, 'on' => true, 'at' => true,
        'by' => true, 'from' => true, 'to' => true, 'with' => true, 'without' => true,
        'about' => true, 'against' => true, 'between' => true, 'into' => true,
        'through' => true, 'during' => true, 'before' => true, 'after' => true,
        'above' => true, 'below' => true, 'up' => true, 'down' => true, 'over' => true,
        'under' => true, 'because' => true, 'as' => true, 'until' => true, 'while' => true,
        'of' => true, 'off' => true, 'then' => true, 'than' => true, 'if' => true,
    ];

    /**
     * Common greeting prefixes that lack value when posted alone.
     */
    protected static array $greetingPatterns = [
        '/^(gm|gn|good\s+morning|good\s+afternoon|good\s+evening|good\s+night|hello\s+everyone|hello\s+all|hi\s+guys|hey\s+guys|happy\s+monday|happy\s+friday|happy\s+weekend|happy\s+new\s+month|happy\s+sunday|welcome\s+back|nice\s+one|nice\s+post|cool\s+post)\b/iu',
    ];

    /**
     * Evaluate the monetization quality of post text.
     *
     * @param  string|null  $content
     * @return array{is_eligible: bool, reason: string}
     */
    public function evaluate(?string $content): array
    {
        if ($content === null || trim($content) === '') {
            return [
                'is_eligible' => false,
                'reason' => 'Post content is empty.',
            ];
        }

        // 1. Clean HTML tags and decode entities
        $cleanText = html_entity_decode(strip_tags($content), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Remove URLs and hashtag symbols for evaluation
        $textWithoutUrls = preg_replace('/\bhttps?:\/\/\S+/i', '', $cleanText);
        $textWithoutUrls = preg_replace('/#([a-zA-Z0-9_]+)/', '$1', $textWithoutUrls);
        $normalizedText = preg_replace('/\s+/u', ' ', trim($textWithoutUrls));

        if ($normalizedText === '') {
            return [
                'is_eligible' => false,
                'reason' => 'Post contains no readable text.',
            ];
        }

        // 2. Formatting Check: All-Caps shouting
        if ($this->isAllCapsShouting($normalizedText)) {
            return [
                'is_eligible' => false,
                'reason' => 'Content appears to be written entirely in uppercase shouting.',
            ];
        }

        // 3. Formatting Check: Excessive punctuation spam
        if (preg_match('/[!?.]{4,}/u', $normalizedText) || preg_match('/[!?]{3,}/u', $normalizedText)) {
            return [
                'is_eligible' => false,
                'reason' => 'Content contains excessive punctuation spam.',
            ];
        }

        // 4. Character flood spam (e.g. "aaaaaa", "hhhhhh", "loooooool")
        if (preg_match('/(.)\1{3,}/u', $normalizedText)) {
            return [
                'is_eligible' => false,
                'reason' => 'Content contains unnatural repeated characters.',
            ];
        }

        // 5. Extract alphanumeric word tokens
        preg_match_all('/[\p{L}\p{N}]+/u', $normalizedText, $matches);
        $words = $matches[0] ?? [];
        $wordCount = count($words);

        // Check word count minimum (5 words)
        if ($wordCount < self::MIN_WORD_COUNT) {
            return [
                'is_eligible' => false,
                'reason' => "Post is too short ({$wordCount} words; minimum " . self::MIN_WORD_COUNT . " meaningful words required for monetization).",
            ];
        }

        // 6. Check for dominance of short 1-2 letter words
        $shortWords = array_filter($words, fn ($w) => mb_strlen($w) <= 2);
        $shortCount = count($shortWords);
        $shortRatio = $shortCount / $wordCount;

        if ($shortRatio >= self::MAX_SHORT_WORD_RATIO) {
            return [
                'is_eligible' => false,
                'reason' => 'Content consists predominantly of 1-2 letter words or short phrases.',
            ];
        }

        // 7. Check substantive words count (length >= 3)
        $substantiveWords = array_filter($words, fn ($w) => mb_strlen($w) >= 3);
        if (count($substantiveWords) < self::MIN_SUBSTANTIVE_WORDS) {
            return [
                'is_eligible' => false,
                'reason' => 'Post lacks sufficient substantive words (at least ' . self::MIN_SUBSTANTIVE_WORDS . ' words with 3+ letters required).',
            ];
        }

        // 8. Check total character substance
        $totalChars = array_sum(array_map('mb_strlen', $words));
        if ($totalChars < self::MIN_CHAR_COUNT) {
            return [
                'is_eligible' => false,
                'reason' => 'Content length is too brief for monetization.',
            ];
        }

        // 9. Check for repetitive word spam (e.g. "nice nice nice nice")
        $lowerWords = array_map('mb_strtolower', $words);
        $uniqueWords = array_unique($lowerWords);
        $uniqueRatio = count($uniqueWords) / $wordCount;

        if ($uniqueRatio < self::MIN_UNIQUE_WORD_RATIO && $wordCount >= 4) {
            return [
                'is_eligible' => false,
                'reason' => 'Content contains excessive word repetition.',
            ];
        }

        // 10. Check for repeated multi-word phrase spam (e.g. "check this out now check this out now")
        if ($this->hasRepeatedPhraseSpam($lowerWords)) {
            return [
                'is_eligible' => false,
                'reason' => 'Content contains repetitive phrase spam.',
            ];
        }

        // 11. Check for gibberish / keyboard mashing
        foreach ($words as $word) {
            $len = mb_strlen($word);
            if ($len >= 5 && preg_match('/^[a-zA-Z]+$/', $word)) {
                // Pure alphabetic word with 5+ chars and no vowels
                if (! preg_match('/[aeiouyAEIOUY]/', $word)) {
                    return [
                        'is_eligible' => false,
                        'reason' => 'Content contains unreadable or gibberish words.',
                    ];
                }
                // 5+ consecutive consonants
                if (preg_match('/[bcdfghjklmnpqrstvwxyz]{5,}/i', $word)) {
                    return [
                        'is_eligible' => false,
                        'reason' => 'Content contains unreadable character sequences.',
                    ];
                }
            }
        }

        // 12. Check for chains of pure function words (prepositions/conjunctions without substance)
        if ($this->isFunctionWordChain($lowerWords)) {
            return [
                'is_eligible' => false,
                'reason' => 'Content consists of fragmented conjunctions or prepositions without substantive meaning.',
            ];
        }

        // 13. Check for superficial greetings without valuable follow-up content
        if ($this->isSuperficialGreetingWithoutValue($normalizedText, $words)) {
            return [
                'is_eligible' => false,
                'reason' => 'Content is a low-effort greeting or salutation lacking informative value.',
            ];
        }

        // 14. Proper Sentence Structure Check: verify presence of a verb or predicate
        if (! $this->containsVerbPredicate($lowerWords)) {
            return [
                'is_eligible' => false,
                'reason' => 'Post does not form a complete sentence (missing an action or verb predicate).',
            ];
        }

        return [
            'is_eligible' => true,
            'reason' => 'Content meets monetization quality criteria.',
        ];
    }

    /**
     * Check if text is all-uppercase screaming.
     */
    protected function isAllCapsShouting(string $text): bool
    {
        preg_match_all('/[a-zA-Z]/', $text, $letters);
        $lettersCount = count($letters[0] ?? []);

        if ($lettersCount < 12) {
            return false;
        }

        preg_match_all('/[A-Z]/', $text, $upperLetters);
        $upperCount = count($upperLetters[0] ?? []);

        return ($upperCount / $lettersCount) >= 0.88;
    }

    /**
     * Verify if the word list contains a grammatical verb or predicate.
     *
     * @param  array<int, string>  $words
     */
    protected function containsVerbPredicate(array $words): bool
    {
        foreach ($words as $word) {
            // Direct match in verb catalog
            if (isset(self::$verbCatalog[$word])) {
                return true;
            }

            $len = mb_strlen($word);

            // Morphological verb endings:
            // 1. -ing (e.g., studying, improving, designing, investing)
            if ($len >= 5 && str_ends_with($word, 'ing')) {
                return true;
            }

            // 2. -ed (e.g., launched, updated, reviewed, discovered)
            if ($len >= 5 && str_ends_with($word, 'ed')) {
                return true;
            }

            // 3. -ize / -ise / -ate / -ify verbs
            if ($len >= 6 && (
                str_ends_with($word, 'ize') || str_ends_with($word, 'izes') || str_ends_with($word, 'ized') ||
                str_ends_with($word, 'ise') || str_ends_with($word, 'ises') || str_ends_with($word, 'ised') ||
                str_ends_with($word, 'ate') || str_ends_with($word, 'ated') || str_ends_with($word, 'ates') ||
                str_ends_with($word, 'ify') || str_ends_with($word, 'ified')
            )) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check whether a post is purely a chain of prepositions/conjunctions.
     *
     * @param  array<int, string>  $words
     */
    protected function isFunctionWordChain(array $words): bool
    {
        $count = count($words);
        if ($count < 4) {
            return false;
        }

        $functionCount = 0;
        foreach ($words as $w) {
            if (isset(self::$functionWords[$w])) {
                $functionCount++;
            }
        }

        return ($functionCount / $count) >= 0.75;
    }

    /**
     * Detect repetitive phrase spam (repeated bigrams or trigrams).
     *
     * @param  array<int, string>  $words
     */
    protected function hasRepeatedPhraseSpam(array $words): bool
    {
        $count = count($words);
        if ($count < 6) {
            return false;
        }

        // Bigram repetition
        $bigrams = [];
        for ($i = 0; $i < $count - 1; $i++) {
            $bg = $words[$i] . ' ' . $words[$i + 1];
            $bigrams[$bg] = ($bigrams[$bg] ?? 0) + 1;
            if ($bigrams[$bg] >= 3) {
                return true;
            }
        }

        // Trigram repetition
        $trigrams = [];
        for ($i = 0; $i < $count - 2; $i++) {
            $tg = $words[$i] . ' ' . $words[$i + 1] . ' ' . $words[$i + 2];
            $trigrams[$tg] = ($trigrams[$tg] ?? 0) + 1;
            if ($trigrams[$tg] >= 2 && $count <= 12) {
                return true;
            }
            if ($trigrams[$tg] >= 3) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if post is a superficial greeting lacking substantial follow-up thoughts.
     *
     * @param  string  $text
     * @param  array<int, string>  $words
     */
    protected function isSuperficialGreetingWithoutValue(string $text, array $words): bool
    {
        foreach (self::$greetingPatterns as $pattern) {
            if (preg_match($pattern, $text, $match)) {
                $greetingStr = $match[0];
                $remainder = trim(substr($text, strlen($greetingStr)));

                // Count words in the remainder
                preg_match_all('/[\p{L}\p{N}]+/u', $remainder, $remMatches);
                $remWords = $remMatches[0] ?? [];
                $substantiveRemWords = array_filter($remWords, fn ($w) => mb_strlen($w) >= 3);

                // If remainder has fewer than 4 substantive words or less than 20 characters, it's just a greeting
                if (count($substantiveRemWords) < 4 || mb_strlen($remainder) < 20) {
                    return true;
                }
            }
        }

        return false;
    }
}
