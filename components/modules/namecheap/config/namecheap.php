<?php
// All available TLDs
Configure::set('Namecheap.tlds', [
    '.co.uk',
    '.com.au',
    '.com.es',
    '.com.pe',
    '.com.sg',
    '.de.com',
    '.me.uk',
    '.net.au',
    '.net.pe',
    '.nom.es',
    '.org.au',
    '.org.es',
    '.org.pe',
    '.org.uk',
    '.us.com',
    '.asia',
    '.biz',
    '.bz',
    '.ca',
    '.cc',
    '.ch',
    '.cm',
    '.co',
    '.com',
    '.de',
    '.es',
    '.eu',
    '.fr',
    '.in',
    '.info',
    '.io',
    '.li',
    '.me',
    '.mobi',
    '.net',
    '.nu',
    '.org',
    '.pe',
    '.pw',
    '.sg',
    '.tv',
    '.us',
    '.ws',
    '.xxx'
]);

// Transfer fields
Configure::set('Namecheap.transfer_fields', [
    'DomainName' => [
        'label' => Language::_('Namecheap.transfer.DomainName', true),
        'type' => 'text'
    ],
    'EPPCode' => [
        'label' => Language::_('Namecheap.transfer.EPPCode', true),
        'type' => 'text'
    ]
]);

// Domain fields
Configure::set('Namecheap.domain_fields', [
    'DomainName' => [
        'label' => Language::_('Namecheap.domain.DomainName', true),
        'type' => 'text'
    ],
]);

// Nameserver fields
Configure::set('Namecheap.nameserver_fields', [
    'ns1' => [
        'label' => Language::_('Namecheap.nameserver.ns1', true),
        'type' => 'text'
    ],
    'ns2' => [
        'label' => Language::_('Namecheap.nameserver.ns2', true),
        'type' => 'text'
    ],
    'ns3' => [
        'label' => Language::_('Namecheap.nameserver.ns3', true),
        'type' => 'text'
    ],
    'ns4' => [
        'label' => Language::_('Namecheap.nameserver.ns4', true),
        'type' => 'text'
    ],
    'ns5' => [
        'label' => Language::_('Namecheap.nameserver.ns5', true),
        'type' => 'text'
    ]
]);

// Whois fields
Configure::set('Namecheap.whois_fields', [
    'RegistrantFirstName' => [
        'label' => Language::_('Namecheap.whois.RegistrantFirstName', true),
        'type' => 'text'
    ],
    'RegistrantLastName' => [
        'label' => Language::_('Namecheap.whois.RegistrantLastName', true),
        'type' => 'text'
    ],
    'RegistrantAddress1' => [
        'label' => Language::_('Namecheap.whois.RegistrantAddress1', true),
        'type' => 'text'
    ],
    'RegistrantAddress2' => [
        'label' => Language::_('Namecheap.whois.RegistrantAddress2', true),
        'type' => 'text'
    ],
    'RegistrantCity' => [
        'label' => Language::_('Namecheap.whois.RegistrantCity', true),
        'type' => 'text'
    ],
    'RegistrantStateProvince' => [
        'label' => Language::_('Namecheap.whois.RegistrantStateProvince', true),
        'type' => 'text'
    ],
    'RegistrantPostalCode' => [
        'label' => Language::_('Namecheap.whois.RegistrantPostalCode', true),
        'type' => 'text'
    ],
    'RegistrantCountry' => [
        'label' => Language::_('Namecheap.whois.RegistrantCountry', true),
        'type' => 'text'
    ],
    'RegistrantPhone' => [
        'label' => Language::_('Namecheap.whois.RegistrantPhone', true),
        'type' => 'text'
    ],
    'RegistrantEmailAddress' => [
        'label' => Language::_('Namecheap.whois.RegistrantEmailAddress', true),
        'type' => 'text'
    ],
    'TechFirstName' => [
        'label' => Language::_('Namecheap.whois.TechFirstName', true),
        'type' => 'text'
    ],
    'TechLastName' => [
        'label' => Language::_('Namecheap.whois.TechLastName', true),
        'type' => 'text'
    ],
    'TechAddress1' => [
        'label' => Language::_('Namecheap.whois.TechAddress1', true),
        'type' => 'text'
    ],
    'TechAddress2' => [
        'label' => Language::_('Namecheap.whois.TechAddress2', true),
        'type' => 'text'
    ],
    'TechCity' => [
        'label' => Language::_('Namecheap.whois.TechCity', true),
        'type' => 'text'
    ],
    'TechStateProvince' => [
        'label' => Language::_('Namecheap.whois.TechStateProvince', true),
        'type' => 'text'
    ],
    'TechPostalCode' => [
        'label' => Language::_('Namecheap.whois.TechPostalCode', true),
        'type' => 'text'
    ],
    'TechCountry' => [
        'label' => Language::_('Namecheap.whois.TechCountry', true),
        'type' => 'text'
    ],
    'TechPhone' => [
        'label' => Language::_('Namecheap.whois.TechPhone', true),
        'type' => 'text'
    ],
    'TechEmailAddress' => [
        'label' => Language::_('Namecheap.whois.TechEmailAddress', true),
        'type' => 'text'
    ],
    'AdminFirstName' => [
        'label' => Language::_('Namecheap.whois.AdminFirstName', true),
        'type' => 'text'
    ],
    'AdminLastName' => [
        'label' => Language::_('Namecheap.whois.AdminLastName', true),
        'type' => 'text'
    ],
    'AdminAddress1' => [
        'label' => Language::_('Namecheap.whois.AdminAddress1', true),
        'type' => 'text'
    ],
    'AdminAddress2' => [
        'label' => Language::_('Namecheap.whois.AdminAddress2', true),
        'type' => 'text'
    ],
    'AdminCity' => [
        'label' => Language::_('Namecheap.whois.AdminCity', true),
        'type' => 'text'
    ],
    'AdminStateProvince' => [
        'label' => Language::_('Namecheap.whois.AdminStateProvince', true),
        'type' => 'text'
    ],
    'AdminPostalCode' => [
        'label' => Language::_('Namecheap.whois.AdminPostalCode', true),
        'type' => 'text'
    ],
    'AdminCountry' => [
        'label' => Language::_('Namecheap.whois.AdminCountry', true),
        'type' => 'text'
    ],
    'AdminPhone' => [
        'label' => Language::_('Namecheap.whois.AdminPhone', true),
        'type' => 'text'
    ],
    'AdminEmailAddress' => [
        'label' => Language::_('Namecheap.whois.AdminEmailAddress', true),
        'type' => 'text'
    ],
    'AuxBillingFirstName' => [
        'label' => Language::_('Namecheap.whois.AuxBillingFirstName', true),
        'type' => 'text'
    ],
    'AuxBillingLastName' => [
        'label' => Language::_('Namecheap.whois.AuxBillingLastName', true),
        'type' => 'text'
    ],
    'AuxBillingAddress1' => [
        'label' => Language::_('Namecheap.whois.AuxBillingAddress1', true),
        'type' => 'text'
    ],
    'AuxBillingAddress2' => [
        'label' => Language::_('Namecheap.whois.AuxBillingAddress2', true),
        'type' => 'text'
    ],
    'AuxBillingCity' => [
        'label' => Language::_('Namecheap.whois.AuxBillingCity', true),
        'type' => 'text'
    ],
    'AuxBillingStateProvince' => [
        'label' => Language::_('Namecheap.whois.AuxBillingStateProvince', true),
        'type' => 'text'
    ],
    'AuxBillingPostalCode' => [
        'label' => Language::_('Namecheap.whois.AuxBillingPostalCode', true),
        'type' => 'text'
    ],
    'AuxBillingCountry' => [
        'label' => Language::_('Namecheap.whois.AuxBillingCountry', true),
        'type' => 'text'
    ],
    'AuxBillingPhone' => [
        'label' => Language::_('Namecheap.whois.AuxBillingPhone', true),
        'type' => 'text'
    ],
    'AuxBillingEmailAddress' => [
        'label' => Language::_('Namecheap.whois.AuxBillingEmailAddress', true),
        'type' => 'text'
    ]
]);

// .US
Configure::set('Namecheap.domain_fields.us', [
    'RegistrantNexus' => [
        'label' => Language::_('Namecheap.domain.RegistrantNexus', true),
        'type' => 'select',
        'options' => [
            'C11' => Language::_('Namecheap.domain.RegistrantNexus.c11', true),
            'C12' => Language::_('Namecheap.domain.RegistrantNexus.c12', true),
            'C21' => Language::_('Namecheap.domain.RegistrantNexus.c21', true),
            'C31' => Language::_('Namecheap.domain.RegistrantNexus.c31', true),
            'C32' => Language::_('Namecheap.domain.RegistrantNexus.c32', true)
        ]
    ],
    'RegistrantPurpose' => [
        'label' => Language::_('Namecheap.domain.RegistrantPurpose', true),
        'type' => 'select',
        'options' => [
            'P1' => Language::_('Namecheap.domain.RegistrantPurpose.p1', true),
            'P2' => Language::_('Namecheap.domain.RegistrantPurpose.p2', true),
            'P3' => Language::_('Namecheap.domain.RegistrantPurpose.p3', true),
            'P4' => Language::_('Namecheap.domain.RegistrantPurpose.p4', true),
            'P5' => Language::_('Namecheap.domain.RegistrantPurpose.p5', true)
        ]
    ]
]);

// .EU
Configure::set('Namecheap.domain_fields.eu', [
    'EUAgreeWhoisPolicy' => [
        'label' => Language::_('Namecheap.domain.EUAgreeWhoisPolicy', true),
        'type' => 'checkbox',
        'options' => [
            'YES' => Language::_('Namecheap.domain.EUAgreeWhoisPolicy.yes', true)
        ]
    ],
    'EUAgreeDeletePolicy' => [
        'label' => Language::_('Namecheap.domain.EUAgreeDeletePolicy', true),
        'type' => 'checkbox',
        'options' => [
            'YES' => Language::_('Namecheap.domain.EUAgreeDeletePolicy.yes', true)
        ]
    ]
]);

// .CA
Configure::set('Namecheap.domain_fields.ca', [
    'CIRALegalType' => [
        'label' => Language::_('Namecheap.domain.CIRALegalType', true),
        'type' => 'select',
        'options' => [
            'CCO' => Language::_('Namecheap.domain.RegistrantPurpose.cco', true),
            'CCT' => Language::_('Namecheap.domain.RegistrantPurpose.cct', true),
            'RES' => Language::_('Namecheap.domain.RegistrantPurpose.res', true),
            'GOV' => Language::_('Namecheap.domain.RegistrantPurpose.gov', true),
            'EDU' => Language::_('Namecheap.domain.RegistrantPurpose.edu', true),
            'ASS' => Language::_('Namecheap.domain.RegistrantPurpose.ass', true),
            'HOP' => Language::_('Namecheap.domain.RegistrantPurpose.hop', true),
            'PRT' => Language::_('Namecheap.domain.RegistrantPurpose.prt', true),
            'TDM' => Language::_('Namecheap.domain.RegistrantPurpose.tdm', true),
            'TRD' => Language::_('Namecheap.domain.RegistrantPurpose.trd', true),
            'PLT' => Language::_('Namecheap.domain.RegistrantPurpose.plt', true),
            'LAM' => Language::_('Namecheap.domain.RegistrantPurpose.lam', true),
            'TRS' => Language::_('Namecheap.domain.RegistrantPurpose.trs', true),
            'ABO' => Language::_('Namecheap.domain.RegistrantPurpose.abo', true),
            'INB' => Language::_('Namecheap.domain.RegistrantPurpose.inb', true),
            'LGR' => Language::_('Namecheap.domain.RegistrantPurpose.lgr', true),
            'OMK' => Language::_('Namecheap.domain.RegistrantPurpose.omk', true),
            'MAJ' => Language::_('Namecheap.domain.RegistrantPurpose.maj', true)
        ]
    ],
    'CIRAWhoisDisplay' => [
        'label' => Language::_('Namecheap.domain.CIRAWhoisDisplay', true),
        'type' => 'select',
        'options' => [
            'Full' => Language::_('Namecheap.domain.CIRAWhoisDisplay.full', true),
            'Private' => Language::_('Namecheap.domain.CIRAWhoisDisplay.private', true),
        ]
    ],
    'CIRAAgreementVersion' => [
        'type' => 'hidden',
        'options' => '2.0'
    ],
    'CIRAAgreementValue' => [
        'type' => 'hidden',
        'options' => 'Y'
    ]
]);

// .CO.UK
Configure::set('Namecheap.domain_fields.co.uk', [
    'COUKLegalType' => [
        'label' => Language::_('Namecheap.domain.COUKLegalType', true),
        'type' => 'select',
        'options' => [
            'IND' => Language::_('Namecheap.domain.COUKLegalType.ind', true),
            'FIND' => Language::_('Namecheap.domain.COUKLegalType.find', true),
            'LTD' => Language::_('Namecheap.domain.COUKLegalType.ltd', true),
            'PLC' => Language::_('Namecheap.domain.COUKLegalType.plc', true),
            'PTNR' => Language::_('Namecheap.domain.COUKLegalType.ptnr', true),
            'LLP' => Language::_('Namecheap.domain.COUKLegalType.llp', true),
            'IP' => Language::_('Namecheap.domain.COUKLegalType.ip', true),
            'STRA' => Language::_('Namecheap.domain.COUKLegalType.stra', true),
            'SCH' => Language::_('Namecheap.domain.COUKLegalType.sch', true),
            'RCHAR' => Language::_('Namecheap.domain.COUKLegalType.rchar', true),
            'GOV' => Language::_('Namecheap.domain.COUKLegalType.gov', true),
            'OTHER' => Language::_('Namecheap.domain.COUKLegalType.other', true),
            'CRC' => Language::_('Namecheap.domain.COUKLegalType.crc', true),
            'FCORP' => Language::_('Namecheap.domain.COUKLegalType.fcorp', true),
            'STAT' => Language::_('Namecheap.domain.COUKLegalType.stat', true),
            'FOTHER' => Language::_('Namecheap.domain.COUKLegalType.fother', true)
        ]
    ],
    'COUKCompanyID' => [
        'label' => Language::_('Namecheap.domain.COUKCompanyID', true),
        'type' => 'text'
    ],
    'COUKRegisteredfor' => [
        'label' => Language::_('Namecheap.domain.COUKRegisteredfor', true),
        'type' => 'text'
    ]
]);

// .ME.UK
Configure::set('Namecheap.domain_fields.me.uk', [
    'MEUKLegalType' => [
        'label' => Language::_('Namecheap.domain.MEUKLegalType', true),
        'type' => 'select',
        'options' => [
            'IND' => Language::_('Namecheap.domain.MEUKLegalType.ind', true),
            'FIND' => Language::_('Namecheap.domain.MEUKLegalType.find', true),
            'LTD' => Language::_('Namecheap.domain.MEUKLegalType.ltd', true),
            'PLC' => Language::_('Namecheap.domain.MEUKLegalType.plc', true),
            'PTNR' => Language::_('Namecheap.domain.MEUKLegalType.ptnr', true),
            'LLP' => Language::_('Namecheap.domain.MEUKLegalType.llp', true),
            'IP' => Language::_('Namecheap.domain.MEUKLegalType.ip', true),
            'STRA' => Language::_('Namecheap.domain.MEUKLegalType.stra', true),
            'SCH' => Language::_('Namecheap.domain.MEUKLegalType.sch', true),
            'RCHAR' => Language::_('Namecheap.domain.MEUKLegalType.rchar', true),
            'GOV' => Language::_('Namecheap.domain.MEUKLegalType.gov', true),
            'OTHER' => Language::_('Namecheap.domain.MEUKLegalType.other', true),
            'CRC' => Language::_('Namecheap.domain.MEUKLegalType.crc', true),
            'FCORP' => Language::_('Namecheap.domain.MEUKLegalType.fcorp', true),
            'STAT' => Language::_('Namecheap.domain.MEUKLegalType.stat', true),
            'FOTHER' => Language::_('Namecheap.domain.MEUKLegalType.fother', true)
        ]
    ],
    'MEUKCompanyID' => [
        'label' => Language::_('Namecheap.domain.MEUKCompanyID', true),
        'type' => 'text'
    ],
    'MEUKRegisteredfor' => [
        'label' => Language::_('Namecheap.domain.MEUKRegisteredfor', true),
        'type' => 'text'
    ]
]);

// .ORG.UK
Configure::set('Namecheap.domain_fields.org.uk', [
    'ORGUKLegalType' => [
        'label' => Language::_('Namecheap.domain.ORGUKLegalType', true),
        'type' => 'select',
        'options' => [
            'IND' => Language::_('Namecheap.domain.ORGUKLegalType.ind', true),
            'FIND' => Language::_('Namecheap.domain.ORGUKLegalType.find', true),
            'LTD' => Language::_('Namecheap.domain.ORGUKLegalType.ltd', true),
            'PLC' => Language::_('Namecheap.domain.ORGUKLegalType.plc', true),
            'PTNR' => Language::_('Namecheap.domain.ORGUKLegalType.ptnr', true),
            'LLP' => Language::_('Namecheap.domain.ORGUKLegalType.llp', true),
            'IP' => Language::_('Namecheap.domain.ORGUKLegalType.ip', true),
            'STRA' => Language::_('Namecheap.domain.ORGUKLegalType.stra', true),
            'SCH' => Language::_('Namecheap.domain.ORGUKLegalType.sch', true),
            'RCHAR' => Language::_('Namecheap.domain.ORGUKLegalType.rchar', true),
            'GOV' => Language::_('Namecheap.domain.ORGUKLegalType.gov', true),
            'OTHER' => Language::_('Namecheap.domain.ORGUKLegalType.other', true),
            'CRC' => Language::_('Namecheap.domain.ORGUKLegalType.crc', true),
            'FCORP' => Language::_('Namecheap.domain.ORGUKLegalType.fcorp', true),
            'STAT' => Language::_('Namecheap.domain.ORGUKLegalType.stat', true),
            'FOTHER' => Language::_('Namecheap.domain.ORGUKLegalType.fother', true)
        ]
    ],
    'ORGUKCompanyID' => [
        'label' => Language::_('Namecheap.domain.ORGUKCompanyID', true),
        'type' => 'text'
    ],
    'ORGUKRegisteredfor' => [
        'label' => Language::_('Namecheap.domain.ORGUKRegisteredfor', true),
        'type' => 'text'
    ]
]);

// .ASIA
Configure::set('Namecheap.domain_fields.asia', [
    'ASIACCLocality' => [
        'type' => 'hidden',
        'options' => null
    ],
    'ASIALegalEntityType' => [
        'label' => Language::_('Namecheap.domain.ASIALegalEntityType', true),
        'type' => 'select',
        'options' => [
            'corporation' => Language::_('Namecheap.domain.ASIALegalEntityType.corporation', true),
            'cooperative' => Language::_('Namecheap.domain.ASIALegalEntityType.cooperative', true),
            'partnership' => Language::_('Namecheap.domain.ASIALegalEntityType.partnership', true),
            'government' => Language::_('Namecheap.domain.ASIALegalEntityType.government', true),
            'politicalParty' => Language::_('Namecheap.domain.ASIALegalEntityType.politicalParty', true),
            'society' => Language::_('Namecheap.domain.ASIALegalEntityType.society', true),
            'institution' => Language::_('Namecheap.domain.ASIALegalEntityType.institution', true),
            'naturalPerson' => Language::_('Namecheap.domain.ASIALegalEntityType.naturalPerson', true)
        ]
    ],
    'ASIAIdentForm' => [
        'label' => Language::_('Namecheap.domain.ASIAIdentForm', true),
        'type' => 'select',
        'options' => [
            'certificate' => Language::_('Namecheap.domain.ASIAIdentForm.certificate', true),
            'legislation' => Language::_('Namecheap.domain.ASIAIdentForm.legislation', true),
            'societyRegistry' => Language::_('Namecheap.domain.ASIAIdentForm.societyRegistry', true),
            'politicalPartyRegistry' => Language::_('Namecheap.domain.ASIAIdentForm.politicalPartyRegistry', true),
            'passport' => Language::_('Namecheap.domain.ASIAIdentForm.passport', true)
        ]
    ],
    'ASIAIdentNumber' => [
        'label' => Language::_('Namecheap.domain.ASIAIdentNumber', true),
        'type' => 'text'
    ]
]);

// .DE
Configure::set('Namecheap.domain_fields.de', [
    'DEConfirmAddress' => [
        'type' => 'hidden',
        'options' => 'DE'
    ],
    'DEAgreeDelete' => [
        'type' => 'hidden',
        'options' => 'YES'
    ]
]);

// .FR
Configure::set('Namecheap.domain_fields.fr', [
    'FRLegalType' => [
        'label' => Language::_('Namecheap.domain.FRLegalType', true),
        'type' => 'select',
        'options' => [
            'Individual' => Language::_('Namecheap.domain.FRLegalType.individual', true),
            'Company' => Language::_('Namecheap.domain.FRLegalType.company', true),
        ]
    ],
    'FRRegistrantBirthDate' => [
        'label' => Language::_('Namecheap.domain.FRRegistrantBirthDate', true),
        'type' => 'text',
        'tooltip' => Language::_('Namecheap.!tooltip.FRRegistrantBirthDate', true)
    ],
    'FRRegistrantBirthplace' => [
        'label' => Language::_('Namecheap.domain.FRRegistrantBirthplace', true),
        'type' => 'text'
    ],
    'FRRegistrantLegalId' => [
        'label' => Language::_('Namecheap.domain.FRRegistrantLegalId', true),
        'type' => 'text',
        'tooltip' => Language::_('Namecheap.!tooltip.FRRegistrantLegalId', true)
    ],
    'FRRegistrantTradeNumber' => [
        'label' => Language::_('Namecheap.domain.FRRegistrantTradeNumber', true),
        'type' => 'text'
    ],
    'FRRegistrantDunsNumber' => [
        'label' => Language::_('Namecheap.domain.FRRegistrantDunsNumber', true),
        'type' => 'text',
        'tooltip' => Language::_('Namecheap.!tooltip.FRRegistrantDunsNumber', true)
    ],
    'FRRegistrantLocalId' => [
        'label' => Language::_('Namecheap.domain.FRRegistrantLocalId', true),
        'type' => 'text'
    ],
    'FRRegistrantJoDateDec' => [
        'label' => Language::_('Namecheap.domain.FRRegistrantJoDateDec', true),
        'type' => 'text',
        'tooltip' => Language::_('Namecheap.!tooltip.FRRegistrantJoDateDec', true)
    ],
    'FRRegistrantJoDatePub' => [
        'label' => Language::_('Namecheap.domain.FRRegistrantJoDatePub', true),
        'type' => 'text',
        'tooltip' => Language::_('Namecheap.!tooltip.FRRegistrantJoDatePub', true)
    ],
    'FRRegistrantJoNumber' => [
        'label' => Language::_('Namecheap.domain.FRRegistrantJoNumber', true),
        'type' => 'text'
    ],
    'FRRegistrantJoPage' => [
        'label' => Language::_('Namecheap.domain.FRRegistrantJoPage', true),
        'type' => 'text'
    ]
]);
