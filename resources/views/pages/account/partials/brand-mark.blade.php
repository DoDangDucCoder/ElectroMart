@switch($brand)
    @case('visa')
        <svg viewBox="0 0 80 26" xmlns="http://www.w3.org/2000/svg" aria-label="Visa">
            <text x="0" y="22" font-family="Arial Black, Arial, sans-serif" font-weight="900" font-size="24" font-style="italic" fill="#1A1F71">VISA</text>
        </svg>
        @break

    @case('mastercard')
        <svg viewBox="0 0 50 32" xmlns="http://www.w3.org/2000/svg" aria-label="Mastercard">
            <circle cx="18" cy="16" r="13" fill="#EB001B"/>
            <circle cx="32" cy="16" r="13" fill="#F79E1B"/>
            <path d="M25 6.5a13 13 0 0 0 0 19 13 13 0 0 0 0-19z" fill="#FF5F00"/>
        </svg>
        @break

    @case('momo')
        <svg viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg" aria-label="MoMo">
            <rect width="40" height="40" rx="8" fill="#A50064"/>
            <text x="20" y="25" text-anchor="middle" font-family="Arial Black, Arial, sans-serif" font-weight="900" font-size="13" fill="#fff">MoMo</text>
        </svg>
        @break

    @case('zalopay')
        <svg viewBox="0 0 90 26" xmlns="http://www.w3.org/2000/svg" aria-label="ZaloPay">
            <text x="0" y="20" font-family="Arial Black, Arial, sans-serif" font-weight="900" font-size="20" fill="#0068FF">Zalo</text>
            <text x="48" y="20" font-family="Arial Black, Arial, sans-serif" font-weight="900" font-size="20" fill="#00C2FF">Pay</text>
        </svg>
        @break

    @case('vietcombank')
        <svg viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg" aria-label="Vietcombank">
            <circle cx="20" cy="20" r="18" fill="#006633"/>
            <path d="M11 14 L20 27 L29 14 L25 14 L20 21 L15 14 Z" fill="#fff"/>
        </svg>
        @break

    @case('qr')
        <svg viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg" aria-label="QR">
            <rect x="3" y="3" width="14" height="14" fill="none" stroke="#1e293b" stroke-width="2"/>
            <rect x="7" y="7" width="6" height="6" fill="#1e293b"/>
            <rect x="23" y="3" width="14" height="14" fill="none" stroke="#1e293b" stroke-width="2"/>
            <rect x="27" y="7" width="6" height="6" fill="#1e293b"/>
            <rect x="3" y="23" width="14" height="14" fill="none" stroke="#1e293b" stroke-width="2"/>
            <rect x="7" y="27" width="6" height="6" fill="#1e293b"/>
            <rect x="22" y="22" width="3" height="3" fill="#1e293b"/>
            <rect x="28" y="22" width="3" height="3" fill="#1e293b"/>
            <rect x="34" y="22" width="3" height="3" fill="#1e293b"/>
            <rect x="22" y="28" width="3" height="3" fill="#1e293b"/>
            <rect x="28" y="28" width="3" height="3" fill="#1e293b"/>
            <rect x="22" y="34" width="3" height="3" fill="#1e293b"/>
            <rect x="34" y="34" width="3" height="3" fill="#1e293b"/>
        </svg>
        @break

    @default
        <i class="fa fa-credit-card"></i>
@endswitch
