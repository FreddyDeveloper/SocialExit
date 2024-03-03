import tkinter as tk
import webbrowser
import pygame

# Initialize pygame mixer
pygame.mixer.init()

# Dictionary containing social media links in English
social_media_en = {
    "Delete X": "https://twitter.com/settings/deactivate",
    "Delete Instagram": "https://www.instagram.com/accounts/remove/request/permanent/",
    "Delete Facebook": "https://www.facebook.com/help/delete_account",
    "Delete TikTok": "https://www.tiktok.com/setting",
    "Delete Twitch": "https://www.twitch.tv/user/delete-account",
    "Delete YouTube": "https://www.youtube.com/account_advanced",
    "Delete Snapchat": "https://accounts.snapchat.com/accounts/delete_account",
    "Delete LinkedIn": "https://www.linkedin.com/mypreferences/d/close-accounts"
}

# Dictionary containing social media links in Spanish
social_media_es = {
    "Elimina X": "https://twitter.com/settings/deactivate",
    "Elimina Instagram": "https://www.instagram.com/accounts/remove/request/permanent/",
    "Elimina Facebook": "https://www.facebook.com/help/delete_account",
    "Elimina TikTok": "https://www.tiktok.com/setting",
    "Elimina Twitch": "https://www.twitch.tv/user/delete-account",
    "Elimina YouTube": "https://www.youtube.com/account_advanced",
    "Elimina Snapchat": "https://accounts.snapchat.com/accounts/delete_account",
    "Elimina LinkedIn": "https://www.linkedin.com/mypreferences/d/close-accounts"
}

social_media = social_media_en

# Function to open a link in the default web browser
def open_link(url):
    # Print the click event to the console
    print(f"Clicked on: {social_media.get(next(key for key, value in social_media.items() if value == url))}")

    # Open the link
    webbrowser.open_new(url)

# Function to change the language
def change_language():
    global social_media
    global language_button
    if language_button["text"] == "Change to English":
        language_button["text"] = "Cambiar a Español"
        social_media = social_media_en
    else:
        language_button["text"] = "Change to English"
        social_media = social_media_es
    refresh_buttons()
    print(f"Changed language to: {'English' if language_button['text'] == 'Change to English' else 'Spanish'}")

# Function to refresh the buttons with the current language
def refresh_buttons():
    for button, url in zip(buttons, social_media.values()):
        button.config(text=list(social_media.keys())[list(social_media.values()).index(url)])

# Function to play sound when the mouse hovers over a button and change the button color to red
def play_hover_sound(event):
    pygame.mixer.Sound("001.mp3").play()
    event.widget.config(bg="red")

# Function to reset the button color when the mouse leaves the button
def reset_button_color(event):
    event.widget.config(bg="#2C2F33")

# Function to play sound when a button is clicked
def play_click_sound(event):
    pygame.mixer.Sound("002.mp3").play()

# Create the main window
root = tk.Tk()
root.title("SocialEXIT")
root.configure(bg="#121212")
root.geometry("225x390")

# Create the language change button
language_button = tk.Button(root, text="Cambiar a Español", bg="orange", fg="black", font=("Arial", 12), width=20, command=change_language)
language_button.pack(pady=10)

# Create a list to hold the buttons
buttons = []

# Create a button for each social media link
for name, url in social_media.items():
    button = tk.Button(root, text=name, bg="#2C2F33", fg="white", font=("Arial", 12), width=20, command=lambda u=url: open_link(u))
    button.pack(pady=5)
    button.bind("<Enter>", play_hover_sound)
    button.bind("<Leave>", reset_button_color)
    button.bind("<Button-1>", play_click_sound)
    buttons.append(button)

# Start the tkinter event loop
root.mainloop()