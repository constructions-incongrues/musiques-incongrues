#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script pour corriger les problèmes d'encodage dans un fichier SQL (version sécurisée)
Usage: python fix_sql_encoding.py input.sql output.sql
"""

import sys
import re
from pathlib import Path

def fix_encoding_in_strings(text):
    """
    Corrige l'encodage uniquement dans les chaînes SQL (entre guillemets)
    """
    def replace_in_string(match):
        quote_char = match.group(1)  # ' ou "
        content = match.group(2)
        
        # Dictionnaire des corrections d'encodage
        corrections = {
            'Ã©': 'é',
            'Ã¨': 'è',
            'Ã ': 'à',
            'Ã§': 'ç',
            'Ã¹': 'ù',
            'Ã¢': 'â',
            'Ãª': 'ê',
            'Ã®': 'î',
            'Ã´': 'ô',
            'Ã»': 'û',
            'Ã¤': 'ä',
            'Ã«': 'ë',
            'Ã¯': 'ï',
            'Ã¶': 'ö',
            'Ã¼': 'ü',
            'Ã±': 'ñ',
            'Ã': 'À',
            'Ã‰': 'É',
            'Ãˆ': 'È',
            'Ã‡': 'Ç',
            'Ã™': 'Ù',
            'Ã‚': 'Â',
            'ÃŠ': 'Ê',
            'ÃŽ': 'Î',
            'Ã"': 'Ô',
            'Ã›': 'Û',
        }
        
        # Applique les corrections uniquement au contenu
        for wrong, correct in corrections.items():
            content = content.replace(wrong, correct)
        
        return quote_char + content + quote_char
    
    # Pattern pour capturer les chaînes SQL échappées correctement
    # Gère les guillemets échappés (\' et \")
    string_pattern = r"(['\"])((?:\\.|(?!\1)[^\\])*)\1"
    
    return re.sub(string_pattern, replace_in_string, text, flags=re.DOTALL)

def remove_problematic_chars(text):
    """
    Supprime uniquement les caractères vraiment problématiques
    """
    # Supprime les emojis (4-bytes UTF-8) qui causent des erreurs MySQL
    # Pattern plus conservateur pour les emojis
    emoji_pattern = re.compile(
        r'[\U0001F600-\U0001F64F\U0001F300-\U0001F5FF\U0001F680-\U0001F6FF\U0001F1E0-\U0001F1FF]+',
        flags=re.UNICODE
    )
    
    def replace_emoji_in_strings(match):
        quote_char = match.group(1)
        content = match.group(2)
        # Supprime les emojis uniquement dans les chaînes
        content = emoji_pattern.sub('', content)
        return quote_char + content + quote_char
    
    string_pattern = r"(['\"])((?:\\.|(?!\1)[^\\])*)\1"
    return re.sub(string_pattern, replace_emoji_in_strings, text, flags=re.DOTALL)

def add_charset_declaration(text):
    """
    Ajoute la déclaration de charset si elle n'existe pas
    """
    # Vérifie si il y a déjà une déclaration SET NAMES
    if not re.search(r'SET\s+NAMES\s+utf8', text, re.IGNORECASE):
        charset_declaration = "/*!40101 SET NAMES utf8mb4 */;\n/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;\n/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;\n\n"
        return charset_declaration + text
    
    return text

def process_sql_file(input_file, output_file):
    """
    Traite le fichier SQL de manière sécurisée
    """
    print(f"Lecture du fichier: {input_file}")
    
    # Essaie différents encodages pour la lecture
    encodings_to_try = ['utf-8', 'latin1', 'cp1252', 'iso-8859-1']
    
    content = None
    used_encoding = None
    
    for encoding in encodings_to_try:
        try:
            with open(input_file, 'r', encoding=encoding, errors='replace') as f:
                content = f.read()
            used_encoding = encoding
            print(f"Fichier lu avec l'encodage: {encoding}")
            break
        except Exception as e:
            print(f"Échec avec {encoding}: {e}")
            continue
    
    if content is None:
        print("Erreur: Impossible de lire le fichier")
        return False
    
    original_size = len(content)
    print(f"Taille du fichier: {original_size} caractères")
    
    print("Correction de l'encodage dans les chaînes de caractères...")
    content = fix_encoding_in_strings(content)
    
    print("Suppression des caractères problématiques...")
    content = remove_problematic_chars(content)
    
    print("Ajout des déclarations de charset...")
    content = add_charset_declaration(content)
    
    # Sauvegarde avec UTF-8
    print(f"Écriture du fichier corrigé: {output_file}")
    try:
        with open(output_file, 'w', encoding='utf-8', newline='') as f:
            f.write(content)
        
        new_size = len(content)
        print(f"Fichier sauvegardé. Taille: {new_size} caractères")
        print("✅ Traitement terminé avec succès!")
        return True
        
    except Exception as e:
        print(f"Erreur lors de l'écriture: {e}")
        return False

def main():
    if len(sys.argv) != 3:
        print("Usage: python fix_sql_encoding.py input.sql output.sql")
        print("Ce script corrige uniquement l'encodage dans les chaînes de caractères")
        print("et préserve la structure SQL intacte.")
        sys.exit(1)

    input_file = sys.argv[1]
    output_file = sys.argv[2]

    if not Path(input_file).exists():
        print(f"Erreur: Le fichier {input_file} n'existe pas")
        sys.exit(1)

    # Créer une sauvegarde
    backup_file = input_file + '.backup'
    try:
        import shutil
        shutil.copy2(input_file, backup_file)
        print(f"Sauvegarde créée: {backup_file}")
    except:
        print("Attention: Impossible de créer une sauvegarde")
    
    success = process_sql_file(input_file, output_file)
    
    if success:
        print(f"\n✅ Fichier corrigé: {output_file}")
        print("\nPour importer:")
        print(f"mysql -u username -p --default-character-set=utf8mb4 database_name < {output_file}")
    else:
        print("❌ Échec du traitement")
        sys.exit(1)

if __name__ == "__main__":
    main()