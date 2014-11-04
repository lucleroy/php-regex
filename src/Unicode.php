<?php

namespace LucLeroy\Regex;

class Unicode
{

    const Letter = 'L';
    const LetterLower = 'Ll';
    const LetterUpper = 'Lu';
    const LetterTitle = 'Lt';
    const LetterModifier = 'Lm';
    const LetterOther = 'Lo';
    const Number = 'N';
    const NumberDecimal = 'Nd';
    const NumberLetter = 'Nl';
    const NumberOther = 'No';
    const Mark = 'M';
    const MarkSpacing = 'Mc';
    const MarkEnclosing = 'Me';
    const MarkNonSpacing = 'Mn';
    const Punctuation = 'P';
    const PunctuationConnector = 'Pc';
    const PunctuationDash = 'Pd';
    const PunctuationClose = 'Pe';
    const PunctuationFinal = 'Pf';
    const PunctuationInitial = 'Pi';
    const PunctuationOpen = 'Ps';
    const PunctuationOther = 'Po';
    const Symbol = 'S';
    const SymbolCurrency = 'Sc';
    const SymbolModifier = 'Sk';
    const SymbolMathematical = 'Sm';
    const SymbolOther = 'So';
    const Separator = 'Z';
    const SeparatorLine = 'Zl';
    const SeparatorParagraph = 'Zp';
    const SeparatorSpace = 'Zs';
    const Other = 'C';
    const Control = 'Cc';
    const Format = 'Cf';
    const Unassigned = 'Cn';
    const PrivateUse = 'Co';
    const Surrogate = 'Cs';
    const ScriptArabic = 'Arabic';
    const ScriptArmenian = 'Armenian';
    const ScriptAvestan = 'Avestan';
    const ScriptBalinese = 'Balinese';
    const ScriptBamum = 'Bamum';
    const ScriptBatak = 'Batak';
    const ScriptBengali = 'Bengali';
    const ScriptBopomofo = 'Bopomofo';
    const ScriptBrahmi = 'Brahmi';
    const ScriptBraille = 'Braille';
    const ScriptBuginese = 'Buginese';
    const ScriptBuhid = 'Buhid';
    const ScriptCanadianAboriginal = 'Canadian_Aboriginal';
    const ScriptCarian = 'Carian';
    const ScriptChakma = 'Chakma';
    const ScriptCham = 'Cham';
    const ScriptCherokee = 'Cherokee';
    const ScriptCommon = 'Common';
    const ScriptCoptic = 'Coptic';
    const ScriptCuneiform = 'Cuneiform';
    const ScriptCypriot = 'Cypriot';
    const ScriptCyrillic = 'Cyrillic';
    const ScriptDeseret = 'Deseret';
    const ScriptDevanagari = 'Devanagari';
    const ScriptEgyptianHieroglyphs = 'Egyptian_Hieroglyphs';
    const ScriptEthiopic = 'Ethiopic';
    const ScriptGeorgian = 'Georgian';
    const ScriptGlagolitic = 'Glagolitic';
    const ScriptGothic = 'Gothic';
    const ScriptGreek = 'Greek';
    const ScriptGujarati = 'Gujarati';
    const ScriptGurmukhi = 'Gurmukhi';
    const ScriptHan = 'Han';
    const ScriptHangul = 'Hangul';
    const ScriptHanunoo = 'Hanunoo';
    const ScriptHebrew = 'Hebrew';
    const ScriptHiragana = 'Hiragana';
    const ScriptImperialAramaic = 'Imperial_Aramaic';
    const ScriptInherited = 'Inherited';
    const ScriptInscriptionalPahlavi = 'Inscriptional_Pahlavi';
    const ScriptInscriptionalParthian = 'Inscriptional_Parthian';
    const ScriptJavanese = 'Javanese';
    const ScriptKaithi = 'Kaithi';
    const ScriptKannada = 'Kannada';
    const ScriptKatakana = 'Katakana';
    const ScriptKayahLi = 'Kayah_Li';
    const ScriptKharoshthi = 'Kharoshthi';
    const ScriptKhmer = 'Khmer';
    const ScriptLao = 'Lao';
    const ScriptLatin = 'Latin';
    const ScriptLepcha = 'Lepcha';
    const ScriptLimbu = 'Limbu';
    const ScriptLinearB = 'Linear_B';
    const ScriptLisu = 'Lisu';
    const ScriptLycian = 'Lycian';
    const ScriptLydian = 'Lydian';
    const ScriptMalayalam = 'Malayalam';
    const ScriptMandaic = 'Mandaic';
    const ScriptMeetei_Mayek = 'Meetei_Mayek';
    const ScriptMeroiticCursive = 'Meroitic_Cursive';
    const ScriptMeroiticHieroglyphs = 'Meroitic_Hieroglyphs';
    const ScriptMiao = 'Miao';
    const ScriptMongolian = 'Mongolian';
    const ScriptMyanmar = 'Myanmar';
    const ScriptNewTaiLue = 'New_Tai_Lue';
    const ScriptNko = 'Nko';
    const ScriptOgham = 'Ogham';
    const ScriptOldItalic = 'Old_Italic';
    const ScriptOldPersian = 'Old_Persian';
    const ScriptOldSouth_Arabian = 'Old_South_Arabian';
    const ScriptOldTurkic = 'Old_Turkic';
    const ScriptOlChiki = 'Ol_Chiki';
    const ScriptOriya = 'Oriya';
    const ScriptOsmanya = 'Osmanya';
    const ScriptPhagsPa = 'Phags_Pa';
    const ScriptPhoenician = 'Phoenician';
    const ScriptRejang = 'Rejang';
    const ScriptRunic = 'Runic';
    const ScriptSamaritan = 'Samaritan';
    const ScriptSaurashtra = 'Saurashtra';
    const ScriptSharada = 'Sharada';
    const ScriptShavian = 'Shavian';
    const ScriptSinhala = 'Sinhala';
    const ScriptSoraSompeng = 'Sora_Sompeng';
    const ScriptSundanese = 'Sundanese';
    const ScriptSylotiNagri = 'Syloti_Nagri';
    const ScriptSyriac = 'Syriac';
    const ScriptTagalog = 'Tagalog';
    const ScriptTagbanwa = 'Tagbanwa';
    const ScriptTaiLe = 'Tai_Le';
    const ScriptTaiTham = 'Tai_Tham';
    const ScriptTaiViet = 'Tai_Viet';
    const ScriptTakri = 'Takri';
    const ScriptTamil = 'Tamil';
    const ScriptTelugu = 'Telugu';
    const ScriptThaana = 'Thaana';
    const ScriptThai = 'Thai';
    const ScriptTibetan = 'Tibetan';
    const ScriptTifinagh = 'Tifinagh';
    const ScriptUgaritic = 'Ugaritic';
    const ScriptVai = 'Vai';
    const ScriptYi = 'Yi';

}
