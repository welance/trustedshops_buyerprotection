# encoding: utf-8


# =============================================================================
# package info
# =============================================================================
NAME = 'symmetrics_module_buyerprotect'

TAGS = ('magento', 'module', 'trustedshops', 'buyerprotection')

LICENSE = 'AFL 3.0'

HOMEPAGE = 'http://www.symmetrics.de'

INSTALL_PATH = ''


# =============================================================================
# responsibilities
# =============================================================================
TEAM_LEADER = {
    'Torsten Walluhn': 'tw@symmetrics.de',
}

MAINTAINER = {
    'Torsten Walluhn': 'tw@symmetrics.de',
}

AUTHORS = {
    'Torsten Walluhn': 'tw@symmetrics.de',
    'Ngoc Anh Doan': 'nd@symmetrics.de',
}

# =============================================================================
# additional infos
# =============================================================================
INFO = 'Trustedshops Käuferschutz Modul'

SUMMARY = '''
Dieses Modul ist noch in Entwicklung und wird dann den Käuferschutz von 
Trusted Shops abwickeln.
'''

NOTES = '''
'''

# =============================================================================
# relations
# =============================================================================
REQUIRES = [
    {'magento': '*', 'magento_enterprise': '*'},
]

EXCLUDES = {
}

VIRTUAL = {
}

DEPENDS_ON_FILES = (
)

PEAR_KEY = ''

COMPATIBLE_WITH = {
    'magento': ['1.4.0.1', '1.4.1.0'],
}
