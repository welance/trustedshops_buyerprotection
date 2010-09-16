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
INFO = 'Trustedshops Buyer Protection'

SUMMARY = '''
This is the second version of the Buyer Protection implementation for Magento.
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
    'magento': ['1.4.0.1', '1.4.1.0', '1.4.1.1'],
}
