# -*- coding: cp1252 -*-
import wx
import os
import sys
from wx.lib.anchors import LayoutAnchors
import wx.stc
#import wxPython.stc # needed names from 2.4 for config file
import re
import keyword
import string
import wx.html
import wx.lib.wxpTag
from time import gmtime, strftime

MY_DEFAULT_SCROLL_WIDTH = 500

if wx.Platform == '__WXMSW__':
    faces = { 'times': 'Times New Roman',
              'mono' : 'Courier New',
              'helv' : 'Arial',
              'other': 'Comic Sans MS',
              'size' : 10,
              'size2': 8,
             }
else:
    faces = { 'times': 'Times',
              'mono' : 'Courier',
              'helv' : 'Helvetica',
              'other': 'new century schoolbook',
              'size' : 12,
              'size2': 10,
             }

wcd = "All files (*.*)|*.*|" \
      "TXT files (*.txt)|*.txt|" \
      "HTML files (*.html)|*.html|" \
      "HTM files (*.htm)|*.htm" \


class MainWindow(wx.Frame):
    def __init__(self, Parent, *args, **kwargs):
        wx.Frame.__init__(self, Parent, *args, **kwargs)
        #set icon (dont forget the setup.py)
        self.icon = wx.Icon(("%s\pycon.ico" % os.getcwd()), wx.BITMAP_TYPE_ICO)
        self.SetIcon(self.icon)
        self.panel = wx.Panel(self)
        self.panel.SetMinSize((300, 400))
        self.text1 = wx.stc.StyledTextCtrl(self.panel, 1
                                 , style=wx.TE_MULTILINE|wx.TE_RICH2|wx.TE_AUTO_URL|wx.SUNKEN_BORDER)
        self.text2 = wx.stc.StyledTextCtrl(self.panel, 1
                                 , style=wx.TE_MULTILINE|wx.TE_RICH2|wx.TE_AUTO_URL|wx.SUNKEN_BORDER)
        self.text1.SetHelpText('The input value you should enter here')
        self.text2.SetHelpText('The output value will appear here')
        self.label1 = wx.StaticText(self.panel, -1, "Enter value to encode:")
        self.label2 = wx.StaticText(self.panel, -1, "Encoded result:")
        self.attrTxt = wx.StaticText(self.panel, -1, "Attributes:")
        self.attributes = wx.TextCtrl(self.panel, -1, size=(-1, -1))
        hsizer = wx.BoxSizer(wx.HORIZONTAL)
        sizer = wx.BoxSizer(wx.VERTICAL)
        #add attributes to e-mail
        targetsList = ['none', '_blank', '_new', '_self', '_parent', '_top']
        self.radioTargets = wx.RadioBox(self.panel, -1, "target", wx.DefaultPosition, wx.DefaultSize
                                        , targetsList, style=wx.RA_SPECIFY_COLS)
        sizer.AddMany([((5, 5), 0, wx.ALIGN_CENTRE|wx.ALL, 5)
                       , (self.radioTargets, 0, wx.EXPAND)
                       , ((5, 5), 0, wx.ALIGN_CENTRE|wx.ALL, 5)
                       , self.attrTxt
                       , (self.attributes, 0, wx.EXPAND|wx.ALL)])
        sizer.AddMany  ([((5, 5), 0, wx.ALIGN_CENTRE|wx.ALL, 5)
                       , (self.label1, 0, wx.EXPAND|wx.ALL, 2)
                       , (self.text1, 1, wx.EXPAND)
                       , ((5, 5), 0, wx.ALIGN_CENTRE|wx.ALL, 5)
                       , (self.label2, 0, wx.EXPAND|wx.ALL, 2)
                       , (self.text2, 1, wx.EXPAND)
                       , (hsizer, 0, wx.EXPAND)])
        self.menuMode = ""
        self.InitVariables()
        self.createMenuBar()
        self.radioTargets.Bind(wx.EVT_RADIOBOX, self.myText1Changes)
        self.text1.Bind(wx.stc.EVT_STC_MODIFIED, self.myText1Changes)
        self.text2.Bind(wx.EVT_KILL_FOCUS, self.myText2Changes)
        self.attributes.Bind(wx.EVT_TEXT, self.myText1Changes)
        self.myMakeStatusBar()
        self.CenterOnScreen()
        self.panel.SetSizer(sizer)
        sizer.Fit(self.panel)
        styleObj = (self.text1, self.text2)
        self.myStyleHTML(styleObj)

        

    #---------------------Init Variables
    def InitVariables(self):
        #last File Name to open
        self.lastFileName = ''
        self.attributes.SetValue("")
        self.fileModify = False
        #define start length of text2 field
        self.text2len = 0
        #call Menu
        self.menuAutoCpb = False
        self.waitingText = ""
        if not self.menuMode:
            self.menuMode = "E-mail"

    #---------------------Create styled windows (obj)
    def myStyleHTML(self, obj):
        if len(obj) > 0:
            for styleObj in obj:
                styleObj.CmdKeyAssign(ord('+'), wx.stc.STC_SCMOD_CTRL, wx.stc.STC_CMD_ZOOMIN)
                styleObj.CmdKeyAssign(ord('-'), wx.stc.STC_SCMOD_CTRL, wx.stc.STC_CMD_ZOOMOUT)
                styleObj.SetConstraints(LayoutAnchors(styleObj, True, True, True, True))
                styleObj.SetScrollWidth(MY_DEFAULT_SCROLL_WIDTH);
                styleObj.SetWrapMode(2)
                styleObj.SetLexer(wx.stc.STC_LEX_HTML)
                styleObj.SetKeyWords(0, " ".join(keyword.kwlist))
                styleObj.SetProperty("fold", "1")
                styleObj.SetProperty("tab.timmy.whinge.level", "1")
                styleObj.SetMargins(0,0)
                styleObj.SetViewWhiteSpace(False)
                # Color the e-mail length
                if styleObj == self.text1:
                    styleObj.SetEdgeColumn(320)
                    if self.menuMode == "E-mail":
                        styleObj.SetEdgeMode(wx.stc.STC_EDGE_BACKGROUND)
                        #rfc2821--4.5.3.1 Size limits and minimums
                        styleObj.SetEdgeColumn(320)
                        styleObj.SetEdgeColour(wx.Colour(255,255,166))
                    else:
                        styleObj.SetEdgeMode(wx.stc.STC_EDGE_NONE)
                # Setup a margin to hold fold markers
                styleObj.SetMarginType(2, wx.stc.STC_MARGIN_SYMBOL)
                styleObj.SetMarginMask(2, wx.stc.STC_MASK_FOLDERS)
                styleObj.SetMarginSensitive(2, True)
                styleObj.SetMarginWidth(2, 6)
                # Make some styles,  The lexer defines what each style is used for, we
                # just have to define what each style looks like.  This set is adapted from
                # Scintilla sample property files.
                # Global default styles for all languages
                styleObj.StyleSetSpec(wx.stc.STC_STYLE_DEFAULT,     "face:%(helv)s,size:%(size)d" % faces)
                styleObj.StyleClearAll()  # Reset all to be like the default
                # Global default styles for all languages
                styleObj.StyleSetSpec(wx.stc.STC_STYLE_DEFAULT,     "face:%(mono)s,size:%(size)d" % faces)
                styleObj.StyleSetSpec(wx.stc.STC_STYLE_LINENUMBER,  "back:#C0C0C0,face:%(mono)s,size:%(size2)d" % faces)
                styleObj.StyleSetSpec(wx.stc.STC_STYLE_CONTROLCHAR, "face:%(other)s" % faces)
                styleObj.StyleSetSpec(wx.stc.STC_STYLE_BRACELIGHT,  "fore:#FFFFFF,back:#0000FF,bold")
                styleObj.StyleSetSpec(wx.stc.STC_STYLE_BRACEBAD,    "fore:#000000,back:#FF0000,bold")
                # HTML styles
                styleObj.StyleSetSpec(wx.stc.STC_H_DEFAULT, "fore:#000000,face:%(mono)s,size:%(size)d" % faces)
                styleObj.StyleSetSpec(wx.stc.STC_H_TAG, "fore:#23B320,face:%(mono)s,bold,size:%(size)d" % faces)
                styleObj.StyleSetSpec(wx.stc.STC_H_TAGUNKNOWN, "fore:#23B320,face:%(mono)s,bold,size:%(size)d" % faces)
                styleObj.StyleSetSpec(wx.stc.STC_H_ATTRIBUTE, "fore:#000000,face:%(mono)s,size:%(size)d" % faces)
                styleObj.StyleSetSpec(wx.stc.STC_H_ATTRIBUTEUNKNOWN, "fore:#000000,face:%(mono)s,size:%(size)d" % faces)
                styleObj.StyleSetSpec(wx.stc.STC_H_NUMBER, "fore:#FF0000,face:%(mono)s,size:%(size)d" % faces)
                styleObj.StyleSetSpec(wx.stc.STC_H_DOUBLESTRING, "fore:#0000FF,face:%(mono)s,size:%(size)d" % faces)
                styleObj.StyleSetSpec(wx.stc.STC_H_SINGLESTRING, "fore:#0000FF,face:%(mono)s,size:%(size)d" % faces)
                styleObj.StyleSetSpec(wx.stc.STC_H_OTHER, "fore:#000000,face:%(mono)s,back:#E0C0E0,eol,size:%(size)d" % faces)
                styleObj.StyleSetSpec(wx.stc.STC_H_COMMENT, "fore:#B2B2B2,face:%(other)s,size:%(size)d" % faces)
                styleObj.StyleSetSpec(wx.stc.STC_H_ENTITY, "fore:#0000FF,face:%(mono)s,size:%(size)d" % faces)
                styleObj.StyleSetSpec(wx.stc.STC_H_VALUE, "fore:#FF0000,face:%(mono)s,bold,size:%(size)d" % faces)
                styleObj.SetCaretWidth(2)
                styleObj.SetCaretForeground("BLUE")
        return

    #---------------------Create menubar
    def createMenuBar(self):
        menuBar = wx.MenuBar()
        for eachMenuData in self.myMenuData():
            menuLabel = eachMenuData[0]
            menuItems = eachMenuData[1]
            menuBar.Append(self.createMenu(menuItems), menuLabel)
        self.SetMenuBar(menuBar)

    #---------------------Create menu
    def createMenu(self, menuData):
        menu = wx.Menu()
        for eachItem in menuData:
            if len(eachItem) == 2:
                label = eachItem[0]
                subMenu = self.createMenu(eachItem[1])
                menu.AppendMenu(wx.NewId(), label, subMenu)
            else:
                self.createMenuItem(menu, *eachItem)
        return menu

    #---------------------Create menuitem
    def createMenuItem(self, menu, label, status, handler, kind=wx.ITEM_NORMAL):
        if not label:
            menu.AppendSeparator()
            return
        menuItem = menu.Append(-1, label, status, kind)
        self.Bind(wx.EVT_MENU, handler, menuItem)

    #---------------------Enter menu content
    def myMenuData(self):
        return [("&File", ( ("&New", "Clear the main window", self.OnNew),
                            ("&Open...", "Open file...", self.OnOpenFile),
                            ("&Save...", "Save file...", self.OnSaveDestinationFile),
                            ("&Save As...", "Save file as...", self.OnSaveAsFile),
                            ("", "", ""),
                            ("&Exit", "Terminate", self.OnExit),
                          )),
                ("&Edit", (
                            ("Cut", "Cut", self.OnCut),
                            ("Copy", "Copy", self.OnCopy),
                            ("Paste", "Paste", self.OnPaste),
                            ("", "", ""),
                            ("Select All", "Select All", self.OnSelectAll),
                            ("Delete", "Delete", self.OnDelete),
                          )),
                ("&Mode", (
                            ("A&uto-Clipboard", "Automatically copy result to Clipboard", self.myOnAutoClipboard, wx.ITEM_CHECK),
                            ("", "", ""),
                            ("E-&mail", "Set to E-mail mode", self.myOnRadioEmail, wx.ITEM_RADIO),
                            ("&Text", "Set to plain Text mode", self.myOnRadioText, wx.ITEM_RADIO),
                            ("&HTML", "Set to HTML mode", self.myOnHtmlText, wx.ITEM_RADIO)
                          )),
                ("&Help", ( ("&Help...", "Program Help...", self.OnHelp),
                            ("", "", ""),
                            ("&About...", "More about this program...", self.OnAbout),
                          ))
                ]

    #---------------------Replace HTML
    def myMultipleReplace(self, target, attributes, emails, text):
        for email in emails:
            emailval = self.myRetCodesString(email, False)
            text = text.replace( email
                                 , "<a href=\"&#109;&#97;&#105;&#108;&#116;&#111;&#58;%s\"%s%s>%s</a>" % (emailval, target, attributes, emailval) )
        return text

    #---------------------Set Autoclipboard
    def myOnAutoClipboard(self, event):
        ###page 244
        if not self.menuAutoCpb:
            self.menuAutoCpb = True
        else:
            self.menuAutoCpb = False
        return

    #---------------------Set radio to EMAIL
    def myOnRadioEmail(self, event):
        #page 346
        self.menuMode = "E-mail"
        self.myText1Changes(wx.stc.EVT_STC_MODIFIED)
        return

    #---------------------Set radio to TEXT
    def myOnRadioText(self, event):
        self.menuMode = "Text"
        self.myText1Changes(wx.stc.EVT_STC_MODIFIED)
        return

    #---------------------Set radio to HTML
    def myOnHtmlText(self, event):
        self.menuMode = "Html"
        self.myText1Changes(wx.stc.EVT_STC_MODIFIED)
        return

    #---------------------Create status bar
    def myMakeStatusBar(self):
        self.statusBar = self.CreateStatusBar()
        self.SetStatusBar(self.statusBar)
        self.statusBar.SetFieldsCount(2)
        #self.statusBar.SetStatusWidth([-1, -2, -3])

    #---------------------Set statuse bar
    def mySetStatusText(self, text = ""):
        self.SetStatusText(text)

    #---------------------Encode to html entities
    def myRetCodesString(self, value, bval):
        if value:
            retValue = ""
            if bval == True:
                value = value[::-1] #reversing string
            while value:
                symbol = value[:1]
                value = value[1:]
                retValue = retValue + ("&#%d;" % ord(symbol))
            if retValue:
                return retValue
            else:
                return ""
        else:
            return ""

    #---------------------Capture changes in the text1 window
    def myText1Changes(self, event):
        textSrc = wx.TextDataObject()
        textSrc.SetText(self.text1.GetText())
        newValFromText1 = textSrc.GetText()
        if newValFromText1:
            if self.menuMode == "E-mail" or self.menuMode == "Html":
                textMatch = re.search(r"\b[a-zA-Z0-9._%-]+@[a-zA-Z0-9._%-]+\.[a-zA-Z]{2,4}\b", newValFromText1)
            elif self.menuMode == "Text":
                textMatch = True
            if textMatch:
                newValEncoded2 = self.myRetCodesString(newValFromText1, False)
                attrValue = self.attributes.GetValue()
                #attributes
                if len(attrValue) > 0:
                    attributes = " " + attrValue
                else:
                    attributes = ""
                #targets
                targetValue = self.radioTargets.GetItemLabel( self.radioTargets.GetSelection() )
                if targetValue == 'none':
                    targetTxt = ""
                else:
                    targetTxt = " target=\"%s\"" % targetValue
                if self.menuMode == "E-mail":
                    newVal2 = "<a href=\"&#109;&#97;&#105;&#108;&#116;&#111;&#58;%s\"%s%s>%s</a>" % (newValEncoded2, targetTxt, attributes, newValEncoded2)
                elif self.menuMode == "Text":
                    newVal2 = newValEncoded2
                elif self.menuMode == "Html":
                    #magic with cleaning any a link in the text
                    reCleanMailPat = re.compile(r'<a\s[^>]*href=(\"??)([^\" >]*?)\1[^>]*>(.*)<\/a>')
                    cleanHtml = reCleanMailPat.sub(r'\3', newValFromText1)
                    #searching for emails list inside html
                    patEmail = re.compile(r'\b([a-zA-Z0-9._%-]+@[a-zA-Z0-9._%-]+\.[a-zA-Z]{2,4})\b', re.IGNORECASE)
                    extractEmail = lambda s: [e for e in re.findall(patEmail, s)]
                    #conver them into lists
                    eMails = extractEmail(cleanHtml)
                    #replace values
                    newHtmlEncoded = self.myMultipleReplace(targetTxt, attributes, eMails, cleanHtml)
                    newVal2 = newHtmlEncoded
                self.text2.SetText(newVal2)
                self.text2.SetSelection(0, 0)
                self.text2len = len(newVal2)
                #set changes marker
                self.fileModify = True
                if len(newValFromText1) < 255:
                    self.mySetStatusText("Entered value: %s" % newValFromText1)
                else:
                    self.mySetStatusText("Entered value is too large")
                if self.menuAutoCpb == True and wx.TheClipboard.Open():
                   clipbText2 = wx.TextDataObject()
                   clipbText2.SetText(newVal2)
                   wx.TheClipboard.SetData(clipbText2)
                   wx.TheClipboard.Close()
                   if self.menuMode == "E-mail":
                       #wx.MessageBox("Copyed to Clipboard", "Information")
                       self.mySetStatusText("Encoded value is copyed to Clipboard")
                   elif self.menuMode == "Text":
                       pass                        
            else:
                self.waitingText = "Waiting for valid e-mail address..."
                #self.text2.SetValue(self.waitingText)
                self.text2.SetText(self.waitingText)
                self.mySetStatusText(self.waitingText)
        else:
            #self.text2.SetValue("")
            self.text2.SetText("")
            self.mySetStatusText("")

    #---------------------Capture changes in the text2 window
    def myText2Changes(self, event):
        text2Src = wx.TextDataObject()
        text2Src.SetText(self.text2.GetText())
        newValFromtext2 = text2Src.GetText()
        newText2len = len(newValFromtext2)
        changesMadeText = "Changes have been made in the second window.\nPlease actualize the entered values..."
        if not newValFromtext2 or newValFromtext2 == self.waitingText:
            pass
        else:
            if newText2len != self.text2len:
                #self.text1.SetValue("")
                self.text1.SetText("")
                wx.MessageBox(changesMadeText, "Warning!")

    #---------------------New File
    def OnNew(self, event):
        self.InitVariables()
        self.text1.SetText("")

    #---------------------Open File
    def OnOpenFile(self, event):
        self.myDoOpenFile()

    def myDoOpenFile(self):
        directory = os.getcwd()
        dirDlg = wx.FileDialog(self, message="Choose a file", defaultDir=directory, defaultFile="",
                               wildcard=wcd, style=wx.OPEN | wx.CHANGE_DIR)
        if dirDlg.ShowModal() == wx.ID_OK:
            path = dirDlg.GetPath()
            try:
                openFile = open(path, "r")
                content = openFile.read()
                openFile.close()
                if self.text1.GetPosition():
                    self.text1.ClearAll()
                self.text1.SetText(content)
                self.lastFileName = path
                self.mySetStatusText("")
                self.fileModify = False
            except IOError, error:
                errDlg = wx.MessageDialog(self, "Error opening file:\n" + str(error))
                errDlg.ShowModal()
            except UnicodeDecodeError, error:
                errDlg = wx.MessageDialog(self, "Error opening file:\n" + str(error))
                errDlg.ShowModal()
        dirDlg.Destroy()


    def OnSaveDestinationFile(self, event):
        self.OnSaveFile(event)

    def OnSaveFile(self, event):
        if self.lastFileName:
            confirmSaveDlg = wx.MessageDialog(self, 'Save file?', 'Confirm',
                                              wx.YES_NO | wx.ICON_QUESTION)
            resConfirmSaveDlg = confirmSaveDlg.ShowModal()
            if resConfirmSaveDlg == wx.ID_YES:
                try:
                    saveFile = open(self.lastFileName, "w")
                    fileText = self.text2.GetText().replace(self.waitingText, "")
                    self.mySetStatusText(os.path.basename(self.lastFileName) + " saved")
                    saveFile.write(fileText)
                    saveFile.close()
                    self.fileModify = False
                    self.mySetStatusText("")
                except IOError, error:
                    dlg = wx.MessageDialog(self, "Error saving file\n" + str(error))
                    dlg.ShowModal()
            else:
                confirmSaveDlg.Destroy()
        else:
            self.OnSaveAsFile(event)

    def OnSaveAsFile(self, event):
        directory = os.getcwd()
        saveDlg = wx.FileDialog(self, message="Save file as...", defaultDir=directory, defaultFile="",
                                wildcard=wcd, style=wx.SAVE | wx.OVERWRITE_PROMPT)
        if saveDlg.ShowModal() == wx.ID_OK:
            savePath = saveDlg.GetPath()
            try:
                saveFile = open(savePath, "w")
                self.lastFileName = os.path.basename(savePath)
                fileText = self.text2.GetText().replace(self.waitingText, "")
                saveFile.write(fileText)
                self.mySetStatusText(self.lastFileName + " saved")
                saveFile.close()
                self.fileModify = False
                self.SetStatusText("")
            except IOError, error:
                errDlg = wx.MessageDialog(self, "Error saving file\n" + str(error))
                errDlg.ShowModal()
        saveDlg.Destroy()

    #---------------------Edit
    def OnCut(self, event):
        self.text2.Cut()
        
    def OnCopy(self, event):
        self.text2.Copy()
        
    def OnPaste(self, event):
        self.text2.Paste()
        
    def OnDelete(self, event):
        #frm, to = self.text2.GetSelection()
        #self.text2.Remove(frm, to)
        self.text2.DeleteBack()

    def OnSelectAll(self, event):
        self.text2.SelectAll()

    #---------------------Help
    def OnHelp(self, event):
        dlg = clsHelp(self)
        dlg.ShowModal()
        dlg.Destroy()

    #---------------------About
    def OnAbout(self, event):
        aboutText = "SZ Universal Encoder © " + strftime("%Y", gmtime()) + " by Sergio Zalyubovskiy"
        aboutDlg = wx.MessageDialog(self, aboutText,
                             "About...", style=wx.OK | wx.ICON_INFORMATION)
        aboutDlg.ShowModal()
        aboutDlg.Destroy()

    #---------------------Exit
    def OnExit(self, e):
        self.Close()

class clsHelp(wx.Dialog):
    text = '''
            <html>
            <body bgcolor="#E1E1EA">
            
            <table bgcolor="#F1F1F1" width="100%%" cellspacing="0" cellpadding="5" border="1">
             <tr>
                <td align="center" valign="middle">
                  <font size="14pt"><b>SZ Universal Encoder © %s Help</b></font>
               </td>
             </tr>
            </table>
            
            <p align="left">
             <font size="-1">
              <b>Usage: </b>With that program you can encode e-mail links in order to prevent their extraction by spam-bots.
             </font>
            </p>
            <p align="left">
            <font size="-1">
             <b>1. </b>Load your source text into first textfield (where says 'Enter your value...'). 
             Immediately after that the result will be displayed in the second field.<br><br>
             <b>2. </b>In the field 'Attributes' you can add any additional source for your links;
             such as style attributes, class attributes etc.<br><br>
             <b>3. </b>You can change the target for all the links in by setting value in the radio set 'target'.<br><br>
             <b>4. </b>It's possible to increase/decrease the text size dinamically focusing your cursor in each window by pressing
             <b>Ctrl-</b><b>/Ctrl+</b>.<br><br>
             <b>5. </b>Change the mode of encoding in toolbar changing the Mode to Text, E-mail or HTML.<br>
             - In the case of E-mail the entered e-mail address will be converted into e-mail link.<br>
             - In the case of Text mode the entered value will be converted into html-entity representation of the text.<br>
             - In the case of Html mode the program will expect the html text copied in the first window o loaded through
             the toolbar 'File' menu.<br><br>
             <b>6. </b>In all cases you can save the encoded result into file; but in the case of loaded Html source
             you'll be able to save this file with all values encoded.<br><br>
             <b>7. </b>You can enable auto-copying to clipboard encoded value by checking Auto-Clipboard flag in Mode menu.<br><br>
             <b>8. </b>If the e-mail address is too large (rfc2821) the part of the text in source window will appear highlighted
             by light-yellow color.<br><br>
            </font>
            </p>
            <p align="center">
             <font size="-1">
              <b>SZ Universal Encoder © %s</b> coded by
               <br>
              <b>Sergio Zalyubovskiy</b> with <b>wxPython © %s</b>
             </font>
             <br>
            <font size="-1">Visit me at: <a href="http://szinet.com" target="_blank">http://szinet.com</a></font>
             <br><br>
             <wxp module="wx" class="Button">
                <param name="label" value="Close">
                <param name="id"    value="ID_OK">
             </wxp>
            </p>
            </body>
            </html>
            '''

    def __init__(self, parent):
        wx.Dialog.__init__(self, parent, -1, "SZ Universal Encoder Help",)
        html = wx.html.HtmlWindow(self, -1, size=(420, -1), style=wx.html.HW_SCROLLBAR_AUTO)
        if "gtk2" in wx.PlatformInfo:
            html.SetStandardFonts()
        html.SetPage(  self.text % ( strftime("%Y", gmtime()), strftime("%Y", gmtime()), strftime("%Y", gmtime()) )  )
        btn = html.FindWindowById(wx.ID_OK)
        ir = html.GetInternalRepresentation()
        #html.SetSize( (ir.GetWidth()+25, ir.GetHeight()+25) )
        html.SetSize( (ir.GetWidth()+25, 350) )
        self.SetClientSize(html.GetSize())
        self.CentreOnParent(wx.BOTH)

        
#---------------------Run self Class
class ExecuteMe(wx.App):
    def mySetTitle(self):
        return "SZ Universal Encoder © " + strftime("%Y", gmtime())
    
    def myCreateApp(self):
        return MainWindow(None, -1, self.mySetTitle(),
                          wx.DefaultPosition, wx.DefaultSize,
                          style=wx.DEFAULT_FRAME_STYLE | wx.NO_FULL_REPAINT_ON_RESIZE)

    def OnInit(self):
        frame = self.myCreateApp()
        frame.Show()
        self.SetTopWindow(frame)
        return True

if __name__ == '__main__':
    launcher = ExecuteMe()
    launcher.MainLoop()
