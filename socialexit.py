"""
SocialEXIT v3.0
─────────────────────────────────────────────
Desktop tool to navigate directly to social-network
account deletion / deactivation pages.

Languages : EN · ES · JA · FR · PT · DE
Framework : PyQt6  +  pygame (sounds)  +  qtawesome (icons)

Requirements:
    pip install PyQt6 pygame qtawesome
"""

import sys, os, webbrowser
from PyQt6.QtWidgets import (
    QApplication, QMainWindow, QWidget, QVBoxLayout, QHBoxLayout,
    QPushButton, QLabel, QScrollArea, QMessageBox,
    QSizePolicy, QFrame,
)
from PyQt6.QtCore import Qt, QSize
from PyQt6.QtGui import QFont, QCursor, QIcon
import qtawesome as qta
import pygame


# ── Resource helper (PyInstaller-safe) ──────────────────────────────
def res(filename: str) -> str:
    base = getattr(sys, "_MEIPASS", os.path.dirname(os.path.abspath(__file__)))
    return os.path.join(base, filename)


# ═══════════════════════════════════════════════════════════════════
#  TRANSLATIONS
# ═══════════════════════════════════════════════════════════════════
LANG_META = {
    "en": {"flag": "🇺🇸", "name": "English"},
    "es": {"flag": "🇪🇸", "name": "Español"},
    "ja": {"flag": "🇯🇵", "name": "日本語"},
    "fr": {"flag": "🇫🇷", "name": "Français"},
    "pt": {"flag": "🇧🇷", "name": "Português"},
    "de": {"flag": "🇩🇪", "name": "Deutsch"},
}

UI = {
    "en": {
        "subtitle": "Take back control of your data.",
        "confirm_title": "Confirm",
        "confirm_msg": "This will open the {network} deletion page in your browser.\nContinue?",
    },
    "es": {
        "subtitle": "Recupera el control de tus datos.",
        "confirm_title": "Confirmar",
        "confirm_msg": "Esto abrirá la página de eliminación de {network} en tu navegador.\n¿Continuar?",
    },
    "ja": {
        "subtitle": "あなたのデータの管理を取り戻しましょう。",
        "confirm_title": "確認",
        "confirm_msg": "{network} の削除ページをブラウザで開きます。\n続行しますか？",
    },
    "fr": {
        "subtitle": "Reprenez le contrôle de vos données.",
        "confirm_title": "Confirmer",
        "confirm_msg": "Cela ouvrira la page de suppression de {network} dans votre navigateur.\nContinuer ?",
    },
    "pt": {
        "subtitle": "Retome o controle dos seus dados.",
        "confirm_title": "Confirmar",
        "confirm_msg": "Isso abrirá a página de exclusão do {network} no seu navegador.\nContinuar?",
    },
    "de": {
        "subtitle": "Übernimm die Kontrolle über deine Daten.",
        "confirm_title": "Bestätigen",
        "confirm_msg": "Dies öffnet die Löschseite von {network} in deinem Browser.\nFortfahren?",
    },
}

VERBS = {
    "en": "Delete",   "es": "Eliminar",  "ja": "削除",
    "fr": "Supprimer", "pt": "Excluir",   "de": "Löschen",
}


# ═══════════════════════════════════════════════════════════════════
#  NETWORK REGISTRY
# ═══════════════════════════════════════════════════════════════════
# (qta_icon, brand_color, display_name, url)
# Every URL points directly to the account deletion / deactivation
# settings page.  Verified August 2026.
NETWORKS = [
    ("ph.x-bold",           "#FFFFFF", "X (Twitter)",
     "https://twitter.com/settings/deactivate"),
    ("fa5b.instagram",      "#E1306C", "Instagram",
     "https://www.instagram.com/accounts/remove/request/permanent/"),
    ("fa5b.facebook",       "#1877F2", "Facebook",
     "https://www.facebook.com/help/delete_account"),
    ("fa5b.tiktok",         "#EE1D52", "TikTok",
     "https://www.tiktok.com/setting"),
    ("fa5b.twitch",         "#9146FF", "Twitch",
     "https://www.twitch.tv/user/delete-account"),
    ("fa5b.youtube",        "#FF0000", "YouTube",
     "https://www.youtube.com/account_advanced"),
    ("fa5b.snapchat-ghost", "#FFFC00", "Snapchat",
     "https://accounts.snapchat.com/accounts/delete_account"),
    ("fa5b.linkedin",       "#0A66C2", "LinkedIn",
     "https://www.linkedin.com/mypreferences/d/close-accounts"),
    ("fa5b.reddit-alien",   "#FF4500", "Reddit",
     "https://www.reddit.com/settings/account"),
    ("fa5b.pinterest",      "#E60023", "Pinterest",
     "https://www.pinterest.com/settings/privacy"),
]


# ═══════════════════════════════════════════════════════════════════
#  PALETTE
# ═══════════════════════════════════════════════════════════════════
C = {
    "bg":          "#0A0A0F",
    "surface":     "#12121C",
    "card":        "#16162A",
    "card_border": "#1E1E38",
    "card_hover":  "#B5302A",
    "accent":      "#E74C3C",
    "accent_glow": "#FF6B6B",
    "text":        "#E8E8F0",
    "text_dim":    "#6C6C80",
    "lang_bg":     "#1A1A2E",
    "lang_active": "#E74C3C",
    "separator":   "#222236",
}

VERSION = "3.0"
ICON_PX  = 20   # icon size in pixels


# ═══════════════════════════════════════════════════════════════════
#  WIDGETS
# ═══════════════════════════════════════════════════════════════════
class SmoothScrollArea(QScrollArea):
    """QScrollArea that intercepts wheel events from child widgets
    so scrolling always works, even when hovering over buttons."""

    def __init__(self, parent=None):
        super().__init__(parent)
        self.setWidgetResizable(True)
        self.setHorizontalScrollBarPolicy(Qt.ScrollBarPolicy.ScrollBarAlwaysOff)
        self.setVerticalScrollBarPolicy(Qt.ScrollBarPolicy.ScrollBarAsNeeded)

    def eventFilter(self, obj, event):
        if event.type() == event.Type.Wheel:
            # Redirect wheel events from children to the scroll bar
            self.verticalScrollBar().setValue(
                self.verticalScrollBar().value()
                - event.angleDelta().y()
            )
            return True
        return super().eventFilter(obj, event)


class NetworkButton(QPushButton):
    """Card button with brand-coloured FA icon and hover effect."""

    def __init__(self, qta_name: str, brand_color: str, parent=None):
        super().__init__(parent)
        self._qta = qta_name
        self._brand = brand_color
        self.setCursor(QCursor(Qt.CursorShape.PointingHandCursor))
        self.setFixedHeight(50)
        self.setSizePolicy(QSizePolicy.Policy.Expanding, QSizePolicy.Policy.Fixed)
        self.setIconSize(QSize(ICON_PX, ICON_PX))
        self._set_icon(brand_color)
        self._style(False)

    def _set_icon(self, color: str):
        self.setIcon(qta.icon(self._qta, color=color))

    def set_label(self, text: str):
        self.setText(f"  {text}")

    def _style(self, hov: bool):
        bg     = C["card_hover"] if hov else C["card"]
        border = C["accent"]     if hov else C["card_border"]
        self.setStyleSheet(f"""
            QPushButton {{
                background-color: {bg};
                color: {C['text']};
                border: 1px solid {border};
                border-radius: 10px;
                padding: 0 14px;
                text-align: left;
                font-size: 13px;
                font-weight: 500;
            }}
        """)

    def enterEvent(self, ev):
        self._style(True)
        self._set_icon("#FFFFFF")
        super().enterEvent(ev)

    def leaveEvent(self, ev):
        self._style(False)
        self._set_icon(self._brand)
        super().leaveEvent(ev)


class LangPill(QPushButton):
    def __init__(self, code: str, flag: str, name: str, parent=None):
        super().__init__(flag, parent)
        self.code = code
        self.setToolTip(name)
        self.setCursor(QCursor(Qt.CursorShape.PointingHandCursor))
        self.setFixedSize(42, 32)
        self.mark(False)

    def mark(self, on: bool):
        bg = C["lang_active"] if on else C["lang_bg"]
        bd = C["accent_glow"] if on else C["separator"]
        self.setStyleSheet(f"""
            QPushButton {{
                background: {bg}; color: {C['text']};
                border: 1px solid {bd}; border-radius: 8px;
                font-size: 16px; padding: 0;
            }}
            QPushButton:hover {{ background: {C['accent']}; }}
        """)


# ═══════════════════════════════════════════════════════════════════
#  MAIN WINDOW
# ═══════════════════════════════════════════════════════════════════
class SocialExitWindow(QMainWindow):

    def __init__(self):
        super().__init__()
        self.lang = "en"
        self.btns: list[NetworkButton] = []
        self.pills: list[LangPill] = []

        # Pre-load sounds once
        pygame.mixer.init()
        try:
            self.snd_h = pygame.mixer.Sound(res("001.mp3"))
            self.snd_c = pygame.mixer.Sound(res("002.mp3"))
        except Exception:
            self.snd_h = self.snd_c = None

        self._ui()
        self._lang_apply()
        self._center()

    # ── build ───────────────────────────────────────────────────────
    def _ui(self):
        self.setWindowTitle("SocialEXIT")
        self.setFixedWidth(380)
        self.setMinimumHeight(660)

        # ── Window icon (replaces default exe icon) ─────────────────
        self.setWindowIcon(qta.icon("mdi6.exit-run", color=C["accent"]))

        cw = QWidget()
        self.setCentralWidget(cw)
        root = QVBoxLayout(cw)
        root.setContentsMargins(0, 0, 0, 0)
        root.setSpacing(0)

        wrap = QWidget()
        wrap.setStyleSheet(f"background:{C['bg']};")
        wl = QVBoxLayout(wrap)
        wl.setContentsMargins(24, 22, 24, 18)
        wl.setSpacing(0)

        # ── header ──────────────────────────────────────────────────
        hdr = QHBoxLayout(); hdr.setSpacing(8)

        t = QLabel("Social")
        t.setStyleSheet(f"color:{C['text']}; font-size:26px; font-weight:300;")
        hdr.addWidget(t)

        t2 = QLabel("EXIT")
        t2.setStyleSheet(f"color:{C['accent']}; font-size:26px; font-weight:800;")
        hdr.addWidget(t2)

        v = QLabel(f"v{VERSION}")
        v.setStyleSheet(f"color:{C['text_dim']}; font-size:10px;")
        v.setAlignment(Qt.AlignmentFlag.AlignBottom)
        hdr.addWidget(v)
        hdr.addStretch()
        wl.addLayout(hdr)

        # ── subtitle ────────────────────────────────────────────────
        self.sub = QLabel()
        self.sub.setStyleSheet(
            f"color:{C['text_dim']}; font-size:11px; padding:2px 0 14px 0;"
        )
        wl.addWidget(self.sub)

        # ── language bar ────────────────────────────────────────────
        lc = QWidget()
        lc.setStyleSheet(
            f"background:{C['surface']}; border-radius:12px;"
        )
        ll = QHBoxLayout(lc)
        ll.setContentsMargins(10, 6, 10, 6); ll.setSpacing(6)

        gl = QLabel()
        gl.setPixmap(
            qta.icon("fa5s.globe", color=C["text_dim"])
            .pixmap(QSize(18, 18))
        )
        ll.addWidget(gl)

        for code, m in LANG_META.items():
            p = LangPill(code, m["flag"], m["name"])
            p.clicked.connect(lambda _, c=code: self._lang_set(c))
            ll.addWidget(p)
            self.pills.append(p)
        ll.addStretch()
        wl.addWidget(lc)

        # ── separator ──────────────────────────────────────────────
        sep = QFrame()
        sep.setFixedHeight(1)
        sep.setStyleSheet(f"background:{C['separator']}; margin:14px 0;")
        wl.addWidget(sep)

        # ── scrollable list ─────────────────────────────────────────
        sa = SmoothScrollArea()
        sa.setStyleSheet(f"""
            QScrollArea {{ border:none; background:transparent; }}
            QScrollBar:vertical {{
                width:6px; background:{C['bg']}; border-radius:3px;
            }}
            QScrollBar::handle:vertical {{
                background:{C['accent']}; border-radius:3px; min-height:40px;
            }}
            QScrollBar::handle:vertical:hover {{
                background:{C['accent_glow']};
            }}
            QScrollBar::add-line:vertical,
            QScrollBar::sub-line:vertical {{ height:0; }}
            QScrollBar::add-page:vertical,
            QScrollBar::sub-page:vertical {{ background:none; }}
        """)

        lw = QWidget()
        lw.setStyleSheet("background:transparent;")
        self.bl = QVBoxLayout(lw)
        self.bl.setContentsMargins(0, 0, 4, 0)
        self.bl.setSpacing(6)

        for qta_name, color, name, url in NETWORKS:
            btn = NetworkButton(qta_name, color)
            btn.clicked.connect(
                lambda _, n=name, u=url: self._click(n, u)
            )
            # hover sound
            orig_enter = btn.enterEvent
            def mk(b, oe):
                def fn(ev):
                    if self.snd_h: self.snd_h.play()
                    oe(ev)
                return fn
            btn.enterEvent = mk(btn, orig_enter)

            # Let scroll area handle wheel events from this button
            btn.installEventFilter(sa)

            self.btns.append(btn)
            self.bl.addWidget(btn)

        self.bl.addStretch()
        sa.setWidget(lw)
        wl.addWidget(sa, 1)

        # ── footer ──────────────────────────────────────────────────
        ft = QLabel("FreddyDeveloper")
        ft.setAlignment(Qt.AlignmentFlag.AlignCenter)
        ft.setStyleSheet(
            f"color:{C['text_dim']}; font-size:9px; padding:14px 0 0 0;"
            f"letter-spacing:1px;"
        )
        wl.addWidget(ft)

        root.addWidget(wrap)

    # ── language ────────────────────────────────────────────────────
    def _lang_set(self, code: str):
        self.lang = code
        self._lang_apply()

    def _lang_apply(self):
        s = UI[self.lang]
        v = VERBS[self.lang]
        self.sub.setText(s["subtitle"])
        for p in self.pills:
            p.mark(p.code == self.lang)
        for btn, (_, _, name, _) in zip(self.btns, NETWORKS):
            btn.set_label(f"{v} {name}")

    # ── click ───────────────────────────────────────────────────────
    def _click(self, name: str, url: str):
        if self.snd_c:
            self.snd_c.play()
        s = UI[self.lang]
        msg = s["confirm_msg"].format(network=name)

        box = QMessageBox(self)
        box.setWindowTitle(s["confirm_title"])
        box.setText(msg)
        box.setIcon(QMessageBox.Icon.Warning)
        box.setStandardButtons(
            QMessageBox.StandardButton.Ok | QMessageBox.StandardButton.Cancel
        )
        box.setStyleSheet(f"""
            QMessageBox {{ background:{C['surface']}; }}
            QLabel {{ color:{C['text']}; font-size:13px; }}
            QPushButton {{
                background:{C['card']}; color:{C['text']};
                border:1px solid {C['separator']}; border-radius:6px;
                padding:6px 20px; font-size:12px;
            }}
            QPushButton:hover {{ background:{C['accent']}; }}
        """)
        if box.exec() == QMessageBox.StandardButton.Ok:
            webbrowser.open_new(url)

    # ── center ──────────────────────────────────────────────────────
    def _center(self):
        scr = QApplication.primaryScreen()
        if scr:
            g = scr.availableGeometry()
            self.move((g.width() - self.width()) // 2,
                      (g.height() - self.height()) // 2)


# ═══════════════════════════════════════════════════════════════════
#  ENTRY POINT
# ═══════════════════════════════════════════════════════════════════
def main():
    app = QApplication(sys.argv)
    app.setFont(QFont("Segoe UI", 10))
    app.setStyleSheet(f"""
        QMainWindow {{ background:{C['bg']}; }}
        QToolTip {{
            background:{C['surface']}; color:{C['text']};
            border:1px solid {C['separator']}; border-radius:4px;
            padding:4px 8px; font-size:11px;
        }}
    """)
    w = SocialExitWindow()
    w.show()
    sys.exit(app.exec())


if __name__ == "__main__":
    main()
